<?php

namespace App\Service\HistoryService;

use App\DTO\History\HistoryItemDTO;
use App\DTO\History\ListenHistoryDTO;
use App\Enum\HistorySource;
use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Http\Resources\Artist\ArtistsResource;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Models\Artist;
use App\Models\ListeningAttempt;
use App\Models\ListeningHistoryItem;
use App\Models\Playlist;
use App\Models\Track;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\History\HistoryRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HistoryService implements HistoryServiceInterface
{
    private int $playCooldown;

    public function __construct(
        private readonly HistoryRepositoryInterface $repository,
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly TrackRepositoryInterface $trackService,
    ) {
        $this->playCooldown = (int) config('services.history_service.play_cooldown', 1);
    }

    /**
     * @deprecated Use listen().
     */
    public function store(int $userId, string $trackId, $sourceId, ?HistorySource $sourceType): void
    {
        $date = Carbon::now();

        if ($sourceType !== null) {
            $source = match ($sourceType) {
                HistorySource::PLAYLIST => $this->playlistRepository->getByUUID($sourceId),
                HistorySource::ARTIST => $this->artistRepository->getByUUID($sourceId),
            };

            if (! $source) {
                throw new NotFoundException('History source not found');
            }

            $sourceId = $source->id;
            $sourceType = $sourceType->getModel();
        }

        $track = $this->trackService->getTrackByUuids([$trackId])->first();

        if (! $track) {
            throw new NotFoundException('Track not found');
        }

        $historyItemDTO = new HistoryItemDTO(
            $userId,
            $track->id,
            $sourceType,
            $sourceId,
        );

        DB::transaction(function () use ($userId, $historyItemDTO, $date): void {
            // Keep the legacy writer serialized with the attempt-based writer.
            DB::table('users')
                ->where('id', $userId)
                ->lockForUpdate()
                ->first();

            $item = $this->repository->get($historyItemDTO, $date);

            if (! $item) {
                $this->repository->store($historyItemDTO);

                return;
            }

            if (
                $item->last_played_at
                && $item->last_played_at <= Carbon::now()->subMinutes($this->playCooldown)
            ) {
                $this->repository->incrementListen($item);
            }
        });
    }

    public function listen(ListenHistoryDTO $dto): array
    {
        $track = $this->trackService->getTrackByUuids([$dto->trackId])->first();

        if (! $track) {
            throw new NotFoundException('Track not found');
        }

        $sourceType = null;
        $sourceId = null;

        if ($dto->source !== null) {
            $source = match ($dto->source) {
                HistorySource::PLAYLIST => $this->playlistRepository->getByUUID($dto->sourceId),
                HistorySource::ARTIST => $this->artistRepository->getByUUID($dto->sourceId),
            };

            if (! $source) {
                throw new NotFoundException('History source not found');
            }

            $sourceType = $dto->source->getModel();
            $sourceId = $source->id;
        }

        return DB::transaction(function () use ($dto, $track, $sourceType, $sourceId): array {
            // Serialize history updates for one user, including concurrent first reports.
            DB::table('users')
                ->where('id', $dto->userId)
                ->lockForUpdate()
                ->first();

            $attempt = ListeningAttempt::query()
                ->with('historyItem')
                ->where('uuid', $dto->attemptId)
                ->lockForUpdate()
                ->first();

            if ($attempt) {
                $historyItem = $attempt->historyItem;

                if (
                    $historyItem->user_id !== $dto->userId
                    || $historyItem->track_id !== $track->id
                    || $historyItem->source_type !== $sourceType
                    || $historyItem->source_id !== $sourceId
                ) {
                    throw new ConflictException('Attempt belongs to another history item');
                }

            } else {
                $playedDate = $dto->startedAt
                    ->setTimezone(config('app.timezone'))
                    ->toDateString();

                $historyItem = ListeningHistoryItem::query()
                    ->where('user_id', $dto->userId)
                    ->where('track_id', $track->id)
                    ->where('source_type', $sourceType)
                    ->where('source_id', $sourceId)
                    ->whereDate('played_date', $playedDate)
                    ->lockForUpdate()
                    ->first();

                if ($historyItem) {
                    $historyItem->increment('play_count', 1, [
                        'last_played_at' => now(),
                    ]);
                    $historyItem->refresh();
                } else {
                    $historyItem = ListeningHistoryItem::query()->create([
                        'user_id' => $dto->userId,
                        'track_id' => $track->id,
                        'source_type' => $sourceType,
                        'source_id' => $sourceId,
                        'played_date' => $playedDate,
                        'last_played_at' => now(),
                        'play_count' => 1,
                        'listened_seconds' => 0,
                    ]);
                }

                $attempt = ListeningAttempt::query()->create([
                    'uuid' => $dto->attemptId,
                    'history_item_id' => $historyItem->id,
                    'credited_seconds' => 0,
                    'started_at' => $dto->startedAt,
                ]);
            }

            $delta = max(0, $dto->listenedSeconds - $attempt->credited_seconds);

            if ($delta > 0) {
                $historyItem->increment('listened_seconds', $delta, [
                    'last_played_at' => now(),
                ]);
                $historyItem->refresh();
                $attempt->credited_seconds = $dto->listenedSeconds;
            }

            $attempt->last_reported_at = now();

            if ($dto->finished) {
                $attempt->finished_at = now();
            }

            $attempt->save();

            return $this->makeListenResult($attempt, $historyItem, $delta);
        });
    }

    private function makeListenResult(
        ListeningAttempt $attempt,
        ListeningHistoryItem $historyItem,
        int $creditedSeconds,
    ): array {
        return [
            'attemptId' => $attempt->uuid,
            'creditedSeconds' => $creditedSeconds,
            'attemptListenedSeconds' => $attempt->credited_seconds,
            'listenedSeconds' => $historyItem->listened_seconds,
            'playCount' => $historyItem->play_count,
            'finished' => $attempt->finished_at !== null,
        ];
    }

    public function get(int $userId, int $limit, int $page): array
    {
        $limit = max(1, min($limit, 50));
        $page = max(1, $page);
        $fromGroup = (($page - 1) * $limit) + 1;
        $toGroup = $page * $limit;

        $historyRows = collect(DB::select(<<<'SQL'
            WITH ordered_history AS (
                SELECT
                    listening_history.*,
                    LAG(id) OVER (ORDER BY last_played_at DESC, id DESC) AS previous_id,
                    LAG(played_date) OVER (ORDER BY last_played_at DESC, id DESC) AS previous_played_date,
                    LAG(source_type) OVER (ORDER BY last_played_at DESC, id DESC) AS previous_source_type,
                    LAG(source_id) OVER (ORDER BY last_played_at DESC, id DESC) AS previous_source_id
                FROM listening_history
                WHERE user_id = ?
            ),
            marked_history AS (
                SELECT
                    *,
                    CASE
                        WHEN previous_id IS NULL THEN 1
                        WHEN played_date IS NOT DISTINCT FROM previous_played_date
                            AND source_type IS NOT DISTINCT FROM previous_source_type
                            AND source_id IS NOT DISTINCT FROM previous_source_id THEN 0
                        ELSE 1
                    END AS is_new_group
                FROM ordered_history
            ),
            grouped_history AS (
                SELECT
                    *,
                    SUM(is_new_group) OVER (ORDER BY last_played_at DESC, id DESC) AS group_number
                FROM marked_history
            )
            SELECT *
            FROM grouped_history
            WHERE group_number BETWEEN ? AND ?
            ORDER BY last_played_at DESC, id DESC
        SQL, [$userId, $fromGroup, $toGroup]));

        if ($historyRows->isEmpty()) {
            return [];
        }

        $trackIds = Track::query()
            ->whereIn('id', $historyRows->pluck('track_id')->unique()->values())
            ->pluck('uuid', 'id');

        $tracks = Track::query()
            ->whereIn('id', $historyRows
                ->whereNull('source_type')
                ->pluck('track_id')
                ->unique()
                ->values())
            ->with('artists')
            ->get()
            ->keyBy('id');

        $playlists = Playlist::query()
            ->whereIn('id', $historyRows
                ->where('source_type', Playlist::class)
                ->pluck('source_id')
                ->unique()
                ->values())
            ->with('playlistType')
            ->get()
            ->keyBy('id');

        $artists = Artist::query()
            ->whereIn('id', $historyRows
                ->where('source_type', Artist::class)
                ->pluck('source_id')
                ->unique()
                ->values())
            ->get()
            ->keyBy('id');

        $historyGroups = $historyRows
            ->groupBy('group_number')
            ->map(fn (Collection $rows) => [
                'date' => (string) $rows->first()->played_date,
                'item' => $this->makeHistoryGroup($rows, $trackIds, $tracks, $playlists, $artists),
            ])
            ->values();

        return $historyGroups
            ->groupBy('date')
            ->map(fn (Collection $groups, string $date) => [
                'date' => $date,
                'items' => $groups->pluck('item')->values(),
            ])
            ->values()
            ->all();
    }

    private function makeHistoryGroup(Collection $rows, Collection $trackIds, Collection $tracks, Collection $playlists, Collection $artists): array
    {
        $first = $rows->first();
        $groupTrackIds = $rows
            ->map(fn ($row) => $trackIds->get($row->track_id))
            ->filter()
            ->values();

        if ($first->source_type === Playlist::class) {
            return [
                'type' => 'playlist',
                'item' => $playlists->has($first->source_id)
                    ? new PlaylistResource($playlists->get($first->source_id))
                    : null,
                'tracksCount' => $groupTrackIds->count(),
                'trackIds' => $groupTrackIds,
            ];
        }

        if ($first->source_type === Artist::class) {
            return [
                'type' => 'artist',
                'item' => $artists->has($first->source_id)
                    ? new ArtistsResource($artists->get($first->source_id))
                    : null,
                'tracksCount' => $groupTrackIds->count(),
                'trackIds' => $groupTrackIds,
            ];
        }

        return [
            'type' => 'tracks',
            'item' => null,
            'tracksCount' => $groupTrackIds->count(),
            'trackIds' => $groupTrackIds,
            'cover' => $this->getTracksGroupCover($rows, $tracks),
        ];
    }

    private function getTracksGroupCover(Collection $rows, Collection $tracks): ?string
    {
        $track = $rows
            ->map(fn ($row) => $tracks->get($row->track_id))
            ->first(fn (?Track $track) => $track?->image);

        return $track ? "/image/track/{$track->image}" : null;
    }
}

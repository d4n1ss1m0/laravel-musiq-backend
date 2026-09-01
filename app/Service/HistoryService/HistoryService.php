<?php

namespace App\Service\HistoryService;

use App\DTO\History\HistoryItemDTO;
use App\Enum\HistorySource;
use App\Http\Resources\Artist\ArtistsResource;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Models\Artist;
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
    )
    {
        $this->playCooldown = (int) config('services.history_service.play_cooldown', 1);
        //        $this->playCooldown = 1;
    }

    public function store(int $userId, string $trackId, $sourceId, HistorySource|null $sourceType): void
    {
        $date = Carbon::now();

        if (!is_null($sourceType)) {
            $sourceId = match ($sourceType) {
                HistorySource::PLAYLIST => $this->playlistRepository->getByUUID($sourceId)->id,
                HistorySource::ARTIST => $this->artistRepository->getByUUID($sourceId)->id,
            };
            $sourceType = $sourceType->getModel();
        }

        $trackId = $this->trackService->getTrackByUuids([$trackId])[0]->id;

        $dto = new HistoryItemDTO(
            $userId,
            $trackId,
            $sourceType,
            $sourceId
        );

        $item = $this->repository->get($dto, $date);

        if (!$item) {
            $this->repository->store($dto);
            return;
        }

        //Проверяем что воспроизведение было позже чем 1 минута, чтобы избежать накрутки наглой
        $lastPlayTime = $item->last_played_at;

        if (
            $lastPlayTime
            && $lastPlayTime <= Carbon::now()->subMinutes($this->playCooldown)
        ) {
            $this->repository->incrementListen($item);
        }
    }

    public function get(int $userId, int $limit, int $page): array
    {
        $limit = max(1, min($limit, 50));
        $page = max(1, $page);
        $fromGroup = (($page - 1) * $limit) + 1;
        $toGroup = $page * $limit;

        $historyRows = collect(DB::select(<<<SQL
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

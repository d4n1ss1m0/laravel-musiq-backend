<?php

namespace App\Service\PlaylistService;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\PlaylistTrackDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Enum\PlaylistOrderType;
use App\Exceptions\NotFoundException;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Models\UserPlaylistSetting;
use App\Repositories\Mediateka\MediatekaRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaylistService implements PlaylistServiceInterface
{
    public function __construct(
        private readonly PlaylistRepositoryInterface $repository,
        private readonly MediatekaRepositoryInterface $mediatekaRepository,
        private readonly FileServiceInterface $imageService,
        private readonly TrackServiceInterface $trackService)
    {
    }

    public function getPlaylist(string $playlistId, ?int $userId = null)
    {
        try {
            $playlist = Playlist::query()
                ->withCount('tracks')
                ->withSum('tracks as tracks_duration', 'time')
                ->where('uuid', $playlistId)
                ->firstOrFail();

            $playlist->sort = $this->resolveSort($playlist, $userId)->value;
            $playlist->canChangeSort = $playlist->type != PlaylistTypes::FAVOURITE->getId();

            return $playlist;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException('Playlist not found');
        }
    }

    public function getTracks(string $playlistId, int $userId, int $perPage = 10, ?string $query = null)
    {
        try {
            $playlist = Playlist::query()->where('uuid', $playlistId)->firstOrFail();

            return $this->repository->getPlaylistTracks($playlistId, $this->resolveSort($playlist, $userId))
                ->with('artists')
                ->where('tracks.name', 'like', '%' . $query . '%')
                ->paginate($perPage);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException('Playlist not found');
        }
    }

    private function resolveSort(Playlist $playlist, ?int $userId): PlaylistOrderType
    {
        if ($playlist->type == PlaylistTypes::FAVOURITE->getId()) {
            return PlaylistOrderType::ADDED;
        }

        if ($userId === null) {
            return PlaylistOrderType::POSITION;
        }

        return UserPlaylistSetting::query()
            ->where('playlist_id', $playlist->id)
            ->where('user_id', $userId)
            ->first()?->sort ?? PlaylistOrderType::POSITION;
    }

    public function getQueue(string $playlistId, int $userId)
    {
        $playlist = $this->getPlaylist($playlistId, $userId);

        return $this->repository->getPlaylistTracks($playlistId, PlaylistOrderType::from($playlist->sort))
            ->select(['track_playlists.id as playlist_item_id', 'tracks.uuid as track_id'])
            ->get()
            ->values()
            ->map(fn ($track, $index) => [
                'playlist_item_id' => $track->playlist_item_id,
                'track_id' => $track->track_id,
                'position' => $index + 1,
            ])->all();
    }

    private function getPlaylistWithAccess($playlistId, $userId)
    {
        return Playlist::where('id', $playlistId)
            ->with(['tracks.artists'])
            ->where(function ($q) use ($userId) {
                $q->orWhereHas('playlistType', function ($q2) use ($userId) {
                    $q2->where('name', PlaylistTypes::PUBLIC->value);
                })
                    ->orWhere('user_id', $userId);
            });
    }

    public function createPlaylist(CreatePlaylistDTO $dto, int $userId): Playlist
    {
        $playlist = $this->repository->create($dto->name, $dto->cover, $userId, $dto->type);
        $this->mediatekaRepository->addMedia($playlist, $userId);
        $playlist = $this->getPlaylist($playlist->uuid, $userId);
        return $playlist;
    }

    public function addTrackToPlaylist(string $playlistId, array $trackIds): void
    {
        $playlist = $this->getPlaylist($playlistId);
        $this->addTracks($playlist, $trackIds);
    }

    private function addTracks(Playlist $playlist, array $trackIds): void
    {
        DB::transaction(function () use ($playlist, $trackIds) {
            $playlistIntId = $playlist->id;
            TrackPlaylist::query()
                ->where('playlist_id', $playlistIntId)
                ->lockForUpdate()
                ->get();
            $lastOrder = TrackPlaylist::where('playlist_id', $playlistIntId)->max('order') ?? 0;
            $tracksArray = Track::query()->whereIn('uuid', $trackIds)->get(['id', 'uuid'])->keyBy('uuid')->toArray();
            $tracks = [];
            foreach ($trackIds as $trackId) {
                $track = $tracksArray[$trackId] ?? null;
                $tracks[] = new PlaylistTrackDTO((int)$track['id'], ++$lastOrder);
            }

            $this->repository->addTracks($playlistIntId, $tracks);
        });
    }

    public function removeTrackFromPlaylist(string $playlistId, array $trackId): void
    {
        $playlistIntId = Playlist::query()->where('uuid', $playlistId)->value('id');
        if (empty($playlistIntId)) {
            throw new NotFoundException('Playlist not found');
        }
        $this->repository->removeTracks($playlistIntId, $trackId);
    }

    public function changeOrder(string $playlistId, string $trackId, int $order)
    {
        $playlistIntId = Playlist::query()->where('uuid', $playlistId)->value('id');
        if (empty($playlistIntId)) {
            throw new NotFoundException('Playlist not found');
        }
        $trackIntId = Track::query()->where('uuid', $trackId)->value('id');
        if (empty($trackIntId)) {
            throw new NotFoundException('Track not found');
        }
        $this->repository->updateOrder($playlistIntId, $trackIntId, $order);
    }

    public function updatePlaylist(string $playlistId, UpdatePlaylistDTO $dto)
    {
        try {
            $playlist = Playlist::query()->where('uuid', $playlistId)->select('id', 'image')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException('Playlist not found');
        }

        if ($dto->cover) {
            $oldPath = $playlist->image;
            if ($oldPath) {
                $this->imageService->deleteFile("image/playlist/$oldPath");
            }
        }

        $this->repository->update($playlist->id, $dto->name, $dto->cover, $dto->type);
    }

    public function deletePlaylist(string $playlistId)
    {
        $playlist = $this->getPlaylist($playlistId);
        if ($playlist->image) {
            $this->imageService->deleteFile("image/playlist/$playlist->image");
        }
        $playlist->tracks()->detach();
        $playlist->delete();
    }

    public function importFromPlaylist(string $fromId, string $toId)
    {
        DB::transaction(function () use ($fromId, $toId) {
            try {
                $fromPlaylistTracks = Playlist::query()
                    ->where('uuid', $fromId)
                    ->firstOrFail()
                    ->tracks()
                    ->orderByPivot('order')->pluck('tracks.id');

                $toPlaylist = Playlist::query()
                    ->where('uuid', $toId)
                    ->select(['id'])
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new NotFoundException('Playlist not found');
            }

            $toPlaylistTracks = $toPlaylist
                ->tracks()
                ->get(['tracks.id'])
                ->keyBy('id')
                ->toArray();

            $toPlaylistIntId = $toPlaylist->id;
            TrackPlaylist::query()
                ->where('playlist_id', $toPlaylistIntId)
                ->lockForUpdate()
                ->get();
            $lastOrder = TrackPlaylist::where('playlist_id', $toPlaylistIntId)->max('order') ?? 0;
            $tracks = [];
            foreach ($fromPlaylistTracks as $fromPlaylistTrack) {
                if(!isset($toPlaylistTracks[(string)$fromPlaylistTrack])) {
                    $tracks[] = new PlaylistTrackDTO((int)$fromPlaylistTrack, ++$lastOrder);
                }
            }

            $this->repository->addTracks($toPlaylistIntId, $tracks);

        });
    }

    public function addToFavourite(int $userId, array $trackIds): void
    {
        try {
            $playlist = Playlist::query()
                ->where('user_id', $userId)
                ->where('type', PlaylistTypes::FAVOURITE->getId())
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException('Playlist not found');
        }

        $this->addTracks($playlist, $trackIds);
    }

    public function removeFromFavourite(int $userId, array $trackIds): void
    {
        $playlistIntId = Playlist::query()
            ->where('user_id', $userId)
            ->where('type', PlaylistTypes::FAVOURITE->getId())
            ->value('id');
        if (empty($playlistIntId)) {
            throw new NotFoundException('Playlist not found');
        }
        $this->repository->removeTracks($playlistIntId, $trackIds);
    }

    public function saveSort(int $userId, string $playlistId, PlaylistOrderType $sort): void
    {
        $playlist = Playlist::query()
            ->where('uuid', $playlistId)
            ->firstOrFail();

        if ($playlist->type == PlaylistTypes::FAVOURITE->getId()) {
            throw ValidationException::withMessages(['order' => 'Сортировка избранного фиксирована.']);
        }

        if ($sort === PlaylistOrderType::POSITION) {
            UserPlaylistSetting::query()
                ->where('user_id', $userId)
                ->where('playlist_id', $playlist->id)
                ->delete();

            return;
        }

        UserPlaylistSetting::query()->upsert([
            'user_id' => $userId,
            'playlist_id' => $playlist->id,
            'sort' => $sort->value,
        ], ['user_id', 'playlist_id'], ['sort']);
    }
}

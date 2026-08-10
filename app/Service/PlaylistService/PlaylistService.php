<?php

namespace App\Service\PlaylistService;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\PlaylistTrackDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PlaylistService implements PlaylistServiceInterface
{
    public function __construct(private readonly PlaylistRepositoryInterface $repository, private readonly FileServiceInterface $imageService, private readonly TrackServiceInterface $trackService)
    {
    }

    public function getPlaylist(string $playlistId)
    {
        try {
            return Playlist::query()->where('uuid', $playlistId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Playlist not found', 404);
        }
    }

    public function getTracks(string $playlistId, int $perPage = 10, ?string $query = null)
    {
        try {
            $tracks = Playlist::query()
                ->where('uuid', $playlistId)
                ->firstOrFail()
                ->tracks()
                ->with('artists')
                ->where('name', 'like', '%' . $query . '%')
                ->orderByPivot('order')
                ->paginate($perPage);
            return $tracks;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Playlist not found', 404);
        }
    }

    public function getQueue(string $playlistId)
    {
        try {
            return TrackPlaylist::query()
                ->join('tracks', 'tracks.id', '=', 'track_playlists.track_id')
                ->join('playlists', 'playlists.id', '=', 'track_playlists.playlist_id')
                ->where('playlists.uuid', $playlistId)
                ->orderBy('track_playlists.order')
                ->get([
                    'track_playlists.id as playlist_item_id',
                    'tracks.uuid as track_id',
                    'track_playlists.order as position',
                ])->toArray();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Playlist not found', 404);
        }
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
        return $playlist;
    }

    public function addTrackToPlaylist(string $playlistId, array $trackIds): void
    {
        DB::transaction(function () use ($playlistId, $trackIds) {
            $playlistIntId = Playlist::query()->where('uuid', $playlistId)->value('id');
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
        $this->repository->removeTracks($playlistIntId, $trackId);
    }

    public function changeOrder(string $playlistId, string $trackId, int $order)
    {
        $playlistIntId = Playlist::query()->where('uuid', $playlistId)->value('id');
        $trackIntId = Track::query()->where('uuid', $trackId)->value('id');
        $this->repository->updateOrder($playlistIntId, $trackIntId, $order);
    }

    public function updatePlaylist(string $playlistId, UpdatePlaylistDTO $dto)
    {
        $playlist = Playlist::query()->where('uuid', $playlistId)->select('id', 'image')->firstOrFail();
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
        $playlist = Playlist::query()->where('uuid', $playlistId)->firstOrFail();
        if ($playlist->image) {
            $this->imageService->deleteFile("image/playlist/$playlist->image");
        }
        $playlist->tracks()->detach();
        $playlist->delete();
    }

    public function importFromPlaylist(string $fromId, string $toId)
    {
        DB::transaction(function () use ($fromId, $toId) {
            $fromPlaylistTracks = Playlist::query()->where('uuid', $fromId)->firstOrFail()->tracks()->orderByPivot('order')->pluck('tracks.id');
            $toPlaylist = Playlist::query()->where('uuid', $toId)->select(['id'])->firstOrFail();
            $toPlaylistTracks = $toPlaylist->tracks()->get(['tracks.id'])->keyBy('id')->toArray();
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
}

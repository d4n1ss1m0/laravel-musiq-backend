<?php

namespace App\Service\PlaylistService;

use App\DTO\CreatePlaylistDTO;
use App\DTO\PlaylistTrackDTO;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\TrackService\TrackService;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlaylistService implements PlaylistServiceInterface
{
    public function __construct(private readonly PlaylistRepositoryInterface $repository, private readonly FileServiceInterface $imageService, private readonly TrackServiceInterface $trackService)
    {
    }

    public function getPlaylist(int $playlistId, int $userId)
    {
        try {
            return $this->getPlaylistWithAccess($playlistId, $userId)
                ->with(['playlistType'])
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Playlist not found', 404);
        }
    }

    public function getTracks(int $playlistId, int $userId)
    {
        try {
            $playlist = $this->getPlaylistWithAccess($playlistId, $userId)
                ->with(['tracks.artists'])
                ->firstOrFail();
            return $playlist->tracks;
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

    public function addTrackToPlaylist(int $playlistId, string $trackId): void
    {
        $lastOrder = TrackPlaylist::where('playlist_id', $playlistId)->max('order') ?? 0;
        $track = Track::query()->where('uuid', $trackId)->firstOrFail('id');
        $tracks = [];
        $tracks[] = new PlaylistTrackDTO((int)$track->id, $lastOrder + 1);
        $this->repository->addTracks($playlistId, $tracks);
    }

    public function removeTrackFromPlaylist(int $playlistId, array $trackId): void
    {
        $this->repository->removeTracks($playlistId, $trackId);

    }

    public function changeOrder(int $playlistId, array $tracks)
    {
        $this->repository->updateOrder($playlistId, $tracks);
    }
}

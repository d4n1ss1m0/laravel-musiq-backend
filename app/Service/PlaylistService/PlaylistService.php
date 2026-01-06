<?php

namespace App\Service\PlaylistService;

use App\DTO\CreatePlaylistDTO;
use App\Models\Playlist;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlaylistService implements PlaylistServiceInterface
{
    public function __construct(private readonly PlaylistRepositoryInterface $repository, private readonly FileServiceInterface $imageService)
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
        $this->repository->addTracks($playlist->id, $dto->tracks);
        return $playlist;
    }
}

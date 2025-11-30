<?php

namespace App\Service\Playlist;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Domain\Entities\Playlist;
use App\Domain\Entities\Track;
use App\Service\Auth\AccountService;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Traits\HttpResponse;
use Laravel\Sanctum\PersonalAccessToken;

class PlaylistService implements PlaylistServiceInterface
{
    use HttpResponse;
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function getPlaylist(int $playlistId)
    {
        $playlist = Playlist::where('id', $playlistId)
            ->with(['tracks.artists', 'playlistType'])
            ->where(function ($q) {
                $q->orWhereHas('playlistType', function ($q2) {
                    $q2->where('name', PlaylistTypes::PUBLIC->value);
                })
                    ->orWhere('user_id', $this->accountService->getAccountId());
            })
            ->first();
        if(!isset($playlist)) {
            throw new \Exception('Playlist not found', 404);
        }


        return new PlaylistResource($playlist);
    }

    public function getTracks(int $playlistId)
    {
        // TODO: Implement getTracks() method.
    }

    public function editPlaylist(int $playlistId)
    {
        // TODO: Implement editPlaylist() method.
    }

    public function deletePlaylist(int $playlistId)
    {
        // TODO: Implement deletePlaylist() method.
    }
}

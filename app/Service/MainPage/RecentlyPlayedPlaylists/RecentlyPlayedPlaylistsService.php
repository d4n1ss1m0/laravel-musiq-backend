<?php

namespace App\Service\MainPage\RecentlyPlayedPlaylists;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\MainPage\RecentlyPlayedPlaylistsResource;
use App\Http\Resources\MainPage\RecentlyPlayedTracksResource;
use App\Service\Auth\AccountService;
use Laravel\Sanctum\PersonalAccessToken;

class RecentlyPlayedPlaylistsService implements RecentlyPlayedPlaylistsServiceInterface
{
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function getRecently() {
        $user = $this->accountService->getCurrentAccount();
        $playlists = $user->recentlyPlayedPlaylists()->get();
        return RecentlyPlayedPlaylistsResource::collection($playlists);

    }
}

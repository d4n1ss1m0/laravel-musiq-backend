<?php

namespace App\Service\MainPage\RecentlyPlayedTracks;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\MainPage\RecentlyPlayedTracksResource;
use App\Http\Resources\TrackResource;
use App\Service\Auth\AccountService;
use Laravel\Sanctum\PersonalAccessToken;

class RecentlyPlayedTracksService implements RecentlyPlayedTracksServiceInterface
{
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function getRecently() {
        $user = $this->accountService->getCurrentAccount();
        $tracksIds = $user->recentlyPlayedTracks()->pluck('tracks.id');
        $tracks = $user->recentlyPlayedTracks()->limit(10)->with('artists')->get();
        return [
            'trackIds' => $tracksIds,
            'tracks' => TrackResource::collection($tracks)
        ];
    }
}

<?php

namespace App\Service\MainPage\RecentlyAddedTracks;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\MainPage\RecentlyAddedTracksResource;
use App\Http\Resources\MainPage\RecentlyPlayedTracksResource;
use App\Http\Resources\TrackResource;
use App\Domain\Entities\Track;
use App\Service\Auth\AccountService;
use Laravel\Sanctum\PersonalAccessToken;

class RecentlyAddedTracksService implements RecentlyAddedTracksServiceInterface
{
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function getRecently() {
        $tracks = Track::limit(10)->orderByDesc('created_at')->get();
        $trackIds = Track::limit(10)->orderByDesc('created_at')->pluck('id');
        return [
            'trackIds' => $trackIds,
            'tracks' => TrackResource::collection($tracks),
        ];

    }
}

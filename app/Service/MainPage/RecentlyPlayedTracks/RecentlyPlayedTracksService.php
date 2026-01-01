<?php

namespace App\Service\MainPage\RecentlyPlayedTracks;


use App\Http\Resources\Tracks\TrackResource;
use App\Models\Auth\User;
use App\Service\AuthService\AuthServiceInterface;

class RecentlyPlayedTracksService implements RecentlyPlayedTracksServiceInterface
{

    public function getRecently(int $userId) {
        $user = User::query()->find($userId);
        $tracks = $user->recentlyPlayedTracks()->limit(10)->with('artists')->get()->keyBy('uuid');

        return [
            'trackIds' => array_keys($tracks->all()),
            'tracks' => TrackResource::collection(array_values($tracks->all())),
        ];
    }
}

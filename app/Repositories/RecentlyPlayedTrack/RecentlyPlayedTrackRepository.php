<?php

namespace App\Repositories\RecentlyPlayedTrack;

use App\Models\RecentlyPlayedTrack;
use App\Models\Track;
use App\Repositories\Track\TrackRepositoryInterface;

class RecentlyPlayedTrackRepository implements RecentlyPlayedTrackRepositoryInterface
{

    public function create(int $userId, int $trackId): void
    {
        RecentlyPlayedTrack::create([
            'user_id' => $userId,
            'track_id' => $trackId,
        ]);
    }
}

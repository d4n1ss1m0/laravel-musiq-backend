<?php

namespace App\Repositories\RecentlyPlayedTrack;

interface RecentlyPlayedTrackRepositoryInterface
{
    public function create(int $userId, int $trackId): void;
}

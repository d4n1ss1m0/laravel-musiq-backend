<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Track;

use App\Domain\Models\Track;
use App\Domain\Repositories\Track\TrackRepositoryInterface;

class TrackRepository implements TrackRepositoryInterface
{

    public function create(string $name, int $time, string $file, string $cover, int $userId)
    {
        $track = Track::create([
            'name' => $name,
            'time' => $time,
            'song' => $file,
            'image' => $cover,
            'is_private' => false,
            'user_id' => $userId,
        ]);
        return $track;
    }
}

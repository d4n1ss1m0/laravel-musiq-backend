<?php

namespace App\Repositories\Track;

use App\Models\Track;

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

    public function getTrackByUuids(array $ids)
    {
        return Track::query()
            ->whereIn('uuid', $ids)
            ->get();
    }
}

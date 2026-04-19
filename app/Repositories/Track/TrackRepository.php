<?php

namespace App\Repositories\Track;

use App\Models\Track;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TrackRepository implements TrackRepositoryInterface
{

    public function create(string $name, int $time, string $file, string $cover, int $userId) : Track
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

    public function getTrackByUuids(array $ids) : Collection
    {
        return Track::query()
            ->whereIn('uuid', $ids)
            ->with('artists')
            ->get();
    }

    public function getAddedByUserId(int $userId, int $perPage = 10) : LengthAwarePaginator
    {
        return Track::query()
            ->where('user_id', $userId)
            ->paginate($perPage);
    }
}

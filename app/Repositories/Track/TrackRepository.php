<?php

namespace App\Repositories\Track;

use App\Models\Track;
use App\Models\TrackImport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TrackRepository implements TrackRepositoryInterface
{

    public function create(string $name, int $time, string $file, ?string $cover, ?string $text, bool $isPrivate, int $userId) : Track
    {
        $track = Track::create([
            'name' => $name,
            'time' => $time,
            'song' => $file,
            'image' => $cover,
            'text' => $text,
            'is_private' => $isPrivate,
            'user_id' => $userId,
        ]);
        return $track;
    }

    public function createImport(string $name, string $file, ?string $cover, ?string $text, bool $isPrivate, int $userId) : TrackImport
    {
        $track = TrackImport::create([
            'name' => $name,
            'song' => $file,
            'image' => $cover,
            'is_private' => $isPrivate,
            'text' => $text,
            'user_id' => $userId,
            'status' => 'pending'
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

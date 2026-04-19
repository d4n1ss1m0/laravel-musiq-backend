<?php

namespace App\Repositories\Track;



use App\Models\Track;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TrackRepositoryInterface
{
    public function create(string $name, int $time, string $file, string $cover, int $userId) : Track;

    public function getTrackByUuids(array $ids) : Collection;

    public function getAddedByUserId(int $userId, int $perPage = 10) : LengthAwarePaginator;
}

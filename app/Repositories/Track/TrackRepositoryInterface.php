<?php

namespace App\Repositories\Track;



use App\Models\Track;
use App\Models\TrackImport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TrackRepositoryInterface
{
    public function create(string $name, int $time, string $file, ?string $cover, ?string $text, bool $isPrivate, int $userId) : Track;
    public function createImport(string $name, string $file, ?string $cover, ?string $text, bool $isPrivate, int $userId): TrackImport;

    public function getTrackByUuids(array $ids) : Collection;

    public function getAddedByUserId(int $userId, int $perPage = 10) : LengthAwarePaginator;
}

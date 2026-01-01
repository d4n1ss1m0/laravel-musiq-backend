<?php

namespace App\Repositories\Track;



interface TrackRepositoryInterface
{
    public function create(string $name, int $time, string $file, string $cover, int $userId);

    public function getTrackByUuids(array $ids);
}

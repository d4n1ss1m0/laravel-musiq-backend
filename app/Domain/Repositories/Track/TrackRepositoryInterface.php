<?php

namespace App\Domain\Repositories\Track;



use App\Domain\Models\Auth\User;

interface TrackRepositoryInterface
{
    public function create(string $name, int $time, string $file, string $cover, int $userId);
}

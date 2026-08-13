<?php

namespace App\Repositories\Artist;



use App\Models\Artist;
use App\Models\Track;
use Illuminate\Support\Collection;

interface ArtistRepositoryInterface
{
    public function create(string $name, string $image);

    public function getByUUID(string $uuid): ?Artist;
    public function getTracks(string $uuid, bool $shuffled = false): Collection;
}

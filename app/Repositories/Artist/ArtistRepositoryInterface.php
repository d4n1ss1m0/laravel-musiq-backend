<?php

namespace App\Repositories\Artist;



use App\Models\Artist;

interface ArtistRepositoryInterface
{
    public function create(string $name, string $image);

    public function getByUUID(string $uuid): ?Artist;
}

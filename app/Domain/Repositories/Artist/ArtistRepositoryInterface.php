<?php

namespace App\Domain\Repositories\Artist;



use App\Domain\Models\Auth\User;

interface ArtistRepositoryInterface
{
    public function create(string $name, string $image);
}

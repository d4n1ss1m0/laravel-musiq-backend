<?php

namespace App\Repositories\Artist;



interface ArtistRepositoryInterface
{
    public function create(string $name, string $image);
}

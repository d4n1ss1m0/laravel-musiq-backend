<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Artist;

use App\Domain\Models\Artist;
use App\Domain\Repositories\Artist\ArtistRepositoryInterface;
use App\Domain\Repositories\Track\TrackRepositoryInterface;

class ArtistRepository implements ArtistRepositoryInterface
{

    public function create(string $name, string $image)
    {
        return Artist::create([
            'name' => $name,
            'image' => $image
        ]);
    }
}

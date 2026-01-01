<?php

namespace App\Repositories\Artist;

use App\Models\Artist;

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

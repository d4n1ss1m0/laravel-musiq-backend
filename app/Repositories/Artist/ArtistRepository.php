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

    public function getByUUID(string $uuid): ?Artist
    {
        return Artist::query()
            ->where('uuid', $uuid)
            ->first();
    }
}

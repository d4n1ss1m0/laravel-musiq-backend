<?php

namespace App\Repositories\Artist;

use App\Models\Artist;
use Illuminate\Support\Collection;

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

    public function getTracks(string $uuid, bool $shuffled = false): Collection
    {
        return Artist::query()
            ->where('uuid', $uuid)
            ->with('tracks', function ($query) use ($shuffled) {
                $query->orderBy('tracks.created_at', 'desc');
            })
            ->first()->tracks;
    }
}

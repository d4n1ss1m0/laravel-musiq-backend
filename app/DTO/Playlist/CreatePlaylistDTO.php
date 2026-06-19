<?php

namespace App\DTO\Playlist;

use App\Shared\Enums\PlaylistTypes;

class CreatePlaylistDTO
{
    public string|null $cover;
    public string $name;
    public array $tracks;
    public PlaylistTypes $type;

    public function __construct(string|null $cover, string|null $name, PlaylistTypes $type = PlaylistTypes::PUBLIC, array $tracks = [])
    {
        $this->cover = $cover;
        $this->name = $name;
        $this->tracks = $tracks;
        $this->type = $type;

    }
}

<?php

namespace App\DTO\Playlist;

use App\Shared\Enums\PlaylistTypes;

class UpdatePlaylistDTO
{
    public string|null $cover;
    public string|null $name;
    public array $tracks;
    public PlaylistTypes|null $type;

    public function __construct(string|null $cover, string|null $name, PlaylistTypes|null $type = PlaylistTypes::PUBLIC, array $tracks = [])
    {
        $this->cover = $cover;
        $this->name = $name;
        $this->tracks = $tracks;
        $this->type = $type;

    }
}

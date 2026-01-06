<?php

namespace App\DTO;

use App\DTO\ArtistDTO;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Http\UploadedFile;

class CreatePlaylistDTO
{
    public string|null $cover;
    public string $name;
    public array $tracks;
    public PlaylistTypes $type;

    public function __construct(string|null $cover, string $name, PlaylistTypes $type = PlaylistTypes::PUBLIC, array $tracks = [])
    {
        $this->cover = $cover;
        $this->name = $name;
        $this->tracks = $tracks;
        $this->type = $type;

    }
}

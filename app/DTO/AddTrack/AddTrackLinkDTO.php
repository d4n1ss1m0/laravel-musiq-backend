<?php

namespace App\DTO\AddTrack;

use App\DTO\ArtistDTO;
use Illuminate\Http\UploadedFile;

class AddTrackLinkDTO
{
    public string $file;
    public string $name;
    /**
     * @var array<ArtistDTO>
     */
    public array $artists;

    public UploadedFile|null $cover;
    public string|null $coverName;

    public function __construct(string $file, string $name, array $artists, UploadedFile|null $cover = null, string|null $coverName = null)
    {
        $this->file = $file;
        $this->name = $name;
        $this->artists = $artists;
        $this->cover = $cover;
        $this->coverName = $coverName;
    }
}

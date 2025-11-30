<?php

namespace App\Application\DTO\AddTrack;

use Illuminate\Http\UploadedFile;

class AddTrackDTO
{
    public UploadedFile $file;
    public string $name;
    /**
     * @var array<ArtistDTO>
     */
    public array $artists;

    public UploadedFile|null $cover;

    public function __construct(UploadedFile $file, string $name, array $artists, UploadedFile|null $cover)
    {
        $this->file = $file;
        $this->name = $name;
        $this->artists = $artists;
        $this->cover = $cover;
    }
}

<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

class ArtistDTO
{
    public int|null $id;
    public string $name;

    public function __construct(string $name, int|null $id)
    {
        $this->name = $name;
        $this->id = $id;
    }
}

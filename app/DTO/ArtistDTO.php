<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

class ArtistDTO
{
    public int|null $id;
    public string|null $name;

    public function __construct(string|null $name, int|null $id)
    {
        $this->name = $name;
        $this->id = $id;
    }
}

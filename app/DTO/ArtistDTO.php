<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

class ArtistDTO
{
    public string|null $id;
    public string|null $name;

    public function __construct(string|null $name, string|null $id)
    {
        $this->name = $name;
        $this->id = $id;
    }
}

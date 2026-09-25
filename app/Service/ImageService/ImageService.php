<?php

namespace App\Service\ImageService;

use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\Storage;

class ImageService implements ImageServiceInterface
{
    public function getImage(string $path) {
        $path = 'image/'.$path;
        //dd($path);
        if (!Storage::exists($path)) {
            throw new NotFoundException('File not found');
        }

        $file = Storage::get($path);
        $type = Storage::mimeType($path);

        // Возвращаем изображение с заголовками
        return [$file, $type];
    }
}

<?php

namespace App\Http\Controllers\ImageService;


use App\Http\Controllers\Controller;
use App\Service\ImageService\ImageServiceInterface;
use App\Shared\Traits\HttpResponse;

class ImageServiceController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ImageServiceInterface $imageService)
    {
    }

    public function getImage(string $type, string $name)
    {
        [$file, $type] = $this->imageService->getImage("{$type}/{$name}");
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=86400');
    }
}

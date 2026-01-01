<?php

namespace App\Http\Controllers\ImageService;


use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\ArtistDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Service\ImageService\ImageServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Traits\HttpResponse;

class ImageServiceController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ImageServiceInterface $imageService)
    {
    }

    public function getImage(string $type, string $name)
    {
        try {
            [$file, $type] = $this->imageService->getImage("{$type}/{$name}");
            return response($file, 200)
                ->header('Content-Type', $type)
                ->header('Cache-Control', 'public, max-age=86400');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}

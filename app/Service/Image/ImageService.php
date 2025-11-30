<?php

namespace App\Service\Image;

use App\Http\Requests\Auth\LoginRequest;
use App\Domain\Entities\Playlist;
use App\Domain\Entities\Track;
use App\Shared\Traits\HttpResponse;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class ImageService implements ImageServiceInterface
{
    use HttpResponse;
    public function __construct()
    {
    }

    public function getTrackCover(int $trackId)
    {
        $track = Track::where('id', $trackId)->first('image');
        $path = 'image/track/'.$track->image;
//        dd($path);
        return $this->getImage($path);
    }

    public function getPlaylistCover(int $playlistId)
    {
        $track = Playlist::where('id', $playlistId)->first('image');
        $path = 'image/playlist/'.$track->image;
//        dd($path);
        return $this->getImage($path);
    }

    public function getImage(string $path) {
        $path = 'image/'.$path;
        //dd($path);
        if (!Storage::exists($path)) {
            $this->error('Image not found', 'error', 404);
        }

        $file = Storage::get($path);
        $type = Storage::mimeType($path);

        // Возвращаем изображение с заголовками
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=86400');
    }
}

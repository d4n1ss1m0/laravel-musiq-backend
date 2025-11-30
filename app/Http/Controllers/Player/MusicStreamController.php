<?php

namespace App\Http\Controllers\Player;

use App\Application\DTO\AddTrack\AddTrackDTO;
use App\Application\DTO\AddTrack\ArtistDTO;
use App\Application\UseCase\AddTrack\AddTrackByFile\AddTrackByFileInterface;
use App\Application\UseCase\Auth\LoginUser\LoginUserInterface;
use App\Application\UseCase\Auth\RefreshToken\RefreshTokenInterface;
use App\Application\UseCase\Player\MusicStream\MusicStream;
use App\Application\UseCase\Player\MusicStream\MusicStreamInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class MusicStreamController extends Controller
{
    use HttpResponse;
    public function stream(int $id, Request $request, MusicStreamInterface $useCase)
    {
        if(request()->headers->has('Range')) {
            $range = request()->headers->get('Range');
            $range = preg_replace('/bytes=/', '', $range);
            $range = explode('-', $range);

            $start = (int)$range[0];
            $end = isset($range[1]) && $range[1] !== '' ? (int)$range[1] : null;
        } else {
            $start = null;
            $end = null;
        };

        [$filePath, $fileSize, $callback, $start, $end] = $useCase->handle($id, $start, $end);
//        dd($end);
//        dd([$filePath, $fileSize, $callback, $start, $end]);

        return $this->musicStream($filePath, $callback, $fileSize, $start, $end);
    }
}

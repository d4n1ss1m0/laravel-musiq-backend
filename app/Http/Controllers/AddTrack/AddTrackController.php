<?php

namespace App\Http\Controllers\AddTrack;

use App\Application\DTO\AddTrack\AddTrackDTO;
use App\Application\DTO\AddTrack\ArtistDTO;
use App\Application\UseCase\AddTrack\AddTrackByFile\AddTrackByFileInterface;
use App\Application\UseCase\Auth\LoginUser\LoginUserInterface;
use App\Application\UseCase\Auth\RefreshToken\RefreshTokenInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class AddTrackController extends Controller
{
    use HttpResponse;
    public function addTrackByFile(AddTrackByFileRequest $request, AddTrackByFileInterface $useCase)
    {
        //TODO: заменить из мидлвейра
        $userId = $request->attributes->get('userId');

        $artistArray = [];
        foreach ($request->get('artists') as $artist) {
            $artistArray[] = new ArtistDTO($artist['name'], $artist['id'] ?? null);
        }

        $trackDto = new AddTrackDTO(
            $request->file('file'),
            $request->get('name'),
            $artistArray,
            $request->file('cover'));

        $trackId = $useCase->handle($trackDto, $userId);

        return $this->success(['trackId' => $trackId]);
    }
}

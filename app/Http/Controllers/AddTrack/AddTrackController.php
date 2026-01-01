<?php

namespace App\Http\Controllers\AddTrack;


use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\ArtistDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Traits\HttpResponse;

class AddTrackController extends Controller
{
    use HttpResponse;
    public function addTrackByFile(AddTrackByFileRequest $request, TrackServiceInterface $useCase)
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

        $trackId = $useCase->addTrackByFile($trackDto, $userId);

        return $this->success(['trackId' => $trackId]);
    }
}

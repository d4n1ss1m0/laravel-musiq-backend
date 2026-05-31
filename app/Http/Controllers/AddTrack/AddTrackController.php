<?php

namespace App\Http\Controllers\AddTrack;


use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\AddTrack\AddTrackLinkDTO;
use App\DTO\ArtistDTO;
use App\Enum\MusicService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Http\Requests\AddTrack\AddTrackByLinkRequest;
use App\Http\Requests\AddTrack\ParseLinkRequest;
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
            $request->input('name'),
            $artistArray,
            $request->file('cover'));

        $trackId = $useCase->addTrackByFile($trackDto, $userId);

        return $this->success(['trackId' => $trackId]);
    }

    public function parseFromLink(ParseLinkRequest $request, TrackServiceInterface $useCase)
    {
        $result = $useCase->parseFromUrl($request->input('link'), MusicService::tryFrom($request->input('service')));
        return $this->success($result);
    }

    public function addAfterParse(AddTrackByLinkRequest $request, TrackServiceInterface $useCase)
    {
        $userId = $request->attributes->get('userId');

        $artistArray = [];
        foreach ($request->get('artists') as $artist) {
            $artistArray[] = new ArtistDTO($artist['name'] ?? null, $artist['id'] ?? null);
        }

        $coverName = null;
        $cover = null;

        if ($request->exists('cover')) {
            $cover = $request->file('cover');
        } else {
            $coverName = $request->input('coverName');
        }

        $trackDto = new AddTrackLinkDTO(
            $request->input('file'),
            $request->input('name'),
            $artistArray,
            $cover,
            $coverName
        );

        $trackId = $useCase->addTrackByLink($trackDto, $userId);

        return $this->success(['trackId' => $trackId]);
    }
}

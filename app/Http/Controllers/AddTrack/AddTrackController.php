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
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;

class AddTrackController extends Controller
{
    use HttpResponse;
    public function addTrackByFile(AddTrackByFileRequest $request, TrackServiceInterface $useCase)
    {
        //TODO: заменить из мидлвейра
        $userId = $request->attributes->get('userId');

        $artistArray = [];
        foreach ($request->get(Fields::ARTISTS) as $artist) {
            $artistArray[] = new ArtistDTO($artist[Fields::NAME] ?? null, $artist[Fields::ID] ?? null);
        }

        $trackDto = new AddTrackDTO(
            $request->file(Fields::FILE),
            $request->input(Fields::NAME),
            $artistArray,
            $request->file(Fields::COVER));

        $useCase->addTrackByFile($trackDto, $userId);

        return $this->success(['message' => 'Track on import']);
    }

    public function parseFromLink(ParseLinkRequest $request, TrackServiceInterface $useCase)
    {
        $result = $useCase->parseFromUrl($request->input(Fields::LINK), MusicService::tryFrom($request->input(Fields::SERVICE)));
        return $this->success($result);
    }

    public function addAfterParse(AddTrackByLinkRequest $request, TrackServiceInterface $useCase)
    {
        $userId = $request->attributes->get('userId');

        $artistArray = [];
        foreach ($request->get(Fields::ARTISTS) as $artist) {
            $artistArray[] = new ArtistDTO($artist[Fields::NAME] ?? null, $artist[Fields::ID] ?? null);
        }

        $coverName = null;
        $cover = null;

        if ($request->exists(Fields::COVER)) {
            $cover = $request->file(Fields::COVER);
        } else {
            $coverName = $request->input(Fields::COVER_NAME);
        }

        $trackDto = new AddTrackLinkDTO(
            $request->input(Fields::FILE),
            $request->input(Fields::NAME),
            $artistArray,
            $cover,
            $coverName
        );

        $useCase->addTrackByLink($trackDto, $userId);

        return $this->success(['message' => 'Track on import']);
    }
}

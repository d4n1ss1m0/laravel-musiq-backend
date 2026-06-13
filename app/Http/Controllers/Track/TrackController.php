<?php

namespace App\Http\Controllers\Track;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\TracksRequest;
use App\Http\Resources\Tracks\TrackResource;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    use HttpResponse;
    public function getTracks(TracksRequest $request, TrackRepositoryInterface $trackRepository)
    {
        //TODO: заменить из мидлвейра
        $idsString = $request->query(Fields::IDS);
        $tracksIds = explode(',', $idsString);
        $tracks = $trackRepository->getTrackByUuids($tracksIds);
        $tracksResources = TrackResource::collection($tracks);
        return $this->success($tracksResources);
    }
}

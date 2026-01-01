<?php

namespace App\Http\Controllers\Track;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tracks\TrackResource;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    use HttpResponse;
    public function getTracks(Request $request, TrackRepositoryInterface $trackRepository)
    {
        //TODO: заменить из мидлвейра
        $idsString = $request->query('ids');
        $tracksIds = explode(',', $idsString);
        $tracks = $trackRepository->getTrackByUuids($tracksIds);
        $tracksResources = TrackResource::collection($tracks);
        return $this->success($tracksResources);
    }
}

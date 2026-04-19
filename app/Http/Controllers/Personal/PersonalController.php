<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tracks\TrackResource;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    use HttpResponse;
    public function getAdded(Request $request, TrackRepositoryInterface $trackRepository)
    {
        $user = $request->attributes->get('userId');
        $tracks = $trackRepository->getAddedByUserId($user);
        $tracksResources = TrackResource::collection($tracks);
        return $this->success($this->paginator($tracksResources, $tracks->total(), $tracks->perPage(), $tracks->currentPage()));
    }
}

<?php

namespace App\Http\Controllers\Personal;

use App\Shared\Fields\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Http\Resources\Tracks\TrackResource;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    use HttpResponse;
    public function getAdded(PaginateRequest $request, TrackRepositoryInterface $trackRepository)
    {
        $perPage = $request->query(Fields::PER_PAGE, config('app.per_page_default'));
        $userId = $request->attributes->get('userId');
        $tracks = $trackRepository->getAddedByUserId($userId, $perPage);
        $tracksResources = TrackResource::collection($tracks);
        $keyValueArray = [
            Fields::ITEMS => $tracksResources,
        ];
        return $this->success($this->paginator($keyValueArray, $tracks->total(), $tracks->perPage(), $tracks->currentPage()));
    }
}

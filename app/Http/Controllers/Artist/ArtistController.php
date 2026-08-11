<?php

namespace App\Http\Controllers\Artist;

use App\Http\Requests\Utility\SearchPaginateRequest;
use App\Shared\Fields\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Http\Resources\Artist\ArtistsResource;
use App\Http\Resources\Tracks\TrackResource;
use App\Models\Artist;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    use HttpResponse;
    public function getArtist(string $uuid)
    {
        $artist = Artist::query()->where('uuid', $uuid)->first();
        if (is_null($artist)) {
            return $this->error('Artist not found', 404);
        }
        return $this->success(new ArtistsResource($artist));
    }

    public function searchArtists(SearchPaginateRequest $request)
    {
        $perPage = $request->query(Fields::PER_PAGE, config('app.per_page_default'));
        $artists = Artist::query();

        if ($request->has('query')) {
            $artists->where('name', 'ilike', '%' . $request->input('query') . '%');
        }

        $artists = $artists->paginate($perPage);

        $artistsItems = ArtistsResource::collection($artists->items());

        $keyValueArray = [
            Fields::ITEMS => $artistsItems,
        ];

        return $this->success($this->paginator($keyValueArray, $artists->total(), $artists->perPage(), $artists->currentPage()));
    }
}

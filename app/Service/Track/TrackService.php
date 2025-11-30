<?php

namespace App\Service\Track;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Search\SearchRequest;
use App\Http\Resources\Artist\ArtistsSearchResource;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Http\Resources\Playlists\PlaylistSearchResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\Tracks\TrackSearchResource;
use App\Domain\Entities\Artist;
use App\Domain\Entities\Playlist;
use App\Domain\Entities\Track;
use App\Service\Auth\AccountService;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TrackService implements TrackServiceInterface
{
    use HttpResponse;
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function getTracks(Request $request) {
        $ids = explode(';', $request->input('ids'));
        $tracks = Track::whereIn('id',$ids)
            ->with('artists')
            ->orderByRaw('array_position(ARRAY[' . implode(',', $ids) . '], id)')
            ->get();
        return TrackResource::collection($tracks);
    }



}

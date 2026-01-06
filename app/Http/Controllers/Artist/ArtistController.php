<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Http\Resources\Artist\ArtistsResource;
use App\Http\Resources\Tracks\TrackResource;
use App\Models\Artist;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    use HttpResponse;
    public function getArtist(int $id)
    {
        $artist = Artist::find($id);
        if (is_null($artist)) {
            return $this->error('Artist not found', 404);
        }
        return $this->success(new ArtistsResource($artist));
    }
}

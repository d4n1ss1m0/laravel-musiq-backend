<?php

namespace App\Service\Artist;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Artist\DetailArtistsResource;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Domain\Entities\Artist;
use App\Domain\Entities\Playlist;
use App\Domain\Entities\Track;
use App\Service\Auth\AccountService;
use App\Shared\Traits\HttpResponse;
use Laravel\Sanctum\PersonalAccessToken;

class ArtistService implements ArtistServiceInterface
{
    use HttpResponse;
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function getArtist(int $artistId)
    {
        $artist = Artist::find($artistId);
        return new DetailArtistsResource($artist);
    }
}

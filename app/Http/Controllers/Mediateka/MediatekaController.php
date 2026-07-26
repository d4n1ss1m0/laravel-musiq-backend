<?php

namespace App\Http\Controllers\Mediateka;

use App\Enum\OrderBy;
use App\Service\MediatekaService\MediatekaServiceInterface;
use App\Shared\Fields\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Http\Resources\Tracks\TrackResource;
use App\Service\MainPage\RecentlyAddedTracks\RecentlyAddedTracksServiceInterface;
use App\Service\MainPage\RecentlyPlayedPlaylists\RecentlyPlayedPlaylistsServiceInterface;
use App\Service\MainPage\RecentlyPlayedTracks\RecentlyPlayedTracksServiceInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class MediatekaController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly MediatekaServiceInterface $mediatekaService)
    {

    }

    public function getMediateka(Request $request)
    {
        try {
            $userId = $request->get('userId');
            $mediateka = $this->mediatekaService->getMediateka($userId, OrderBy::CREATED_AT, $request->get('query'));
            dd($mediateka);
        } catch ( \Exception $e) {
            dd($e);
            return $this->error("");
        }
    }
}

<?php

namespace App\Service\Search;

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
use App\Shared\Enums\SearchTypes;
use App\Shared\Traits\HttpResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class SearchService implements SearchServiceInterface
{
    use HttpResponse;
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function search(SearchRequest $request) {
        $search = $request->input('search', null);
        $type = SearchTypes::tryFrom($request->input('type', 'all'));
        $page = $request->input('page', '1');
        $count = $request->input('perPage', '10');
        $results = $this->getSearchResults($search, $type, $page, $count);
        $trackResults = [];
        $artistsResults = [];
        $playlistsResults = [];
        foreach ($results as $result) {
            match ($result->type) {
                SearchTypes::TRACK->value => $trackResults[] = $result,
                SearchTypes::ARTIST->value => $artistsResults[] = $result,
                SearchTypes::PLAYLIST->value => $playlistsResults[] = $result,
            };
        }
        //dd($trackResults);
        $tracks = $this->getTracks($trackResults);
        $playlists = $this->getPlaylists($playlistsResults);
        $artists = $this->getArtists($artistsResults);
        //dd($tracks->where('id', 3));
        $resultArray = [];
        foreach ($results as $result) {
            $resultArray[] = match ($result->type) {
                SearchTypes::TRACK->value => $tracks->where('id', $result->id)->first(),
                SearchTypes::ARTIST->value => $artists->where('id', $result->id)->first(),
                SearchTypes::PLAYLIST->value => $playlists->where('id', $result->id)->first(),
            };
        }
        //dd($resultArray);
        return $resultArray;
    }

    private function getSearchResults(string $search, SearchTypes $searchType, int $page, int $count) {
        $query =  match ($searchType) {
            SearchTypes::ALL => $this->getResultsTypeAll($search),
            SearchTypes::TRACK => $this->getResultsTypeTrack($search),
            SearchTypes::ARTIST => $this->getResultsTypeArtist($search),
            SearchTypes::PLAYLIST => $this->getResultsTypePlaylist($search),
        };
        $query = $query->orderBy('created_at')->paginate($count, ['*'], 'page', $page);
        return $query->items();
    }

    private function getResultsTypeAll($search) {
        return DB::table('tracks')
            ->select('id', 'name','created_at', DB::raw("'track' as type"))
            ->where('name', 'like', "%{$search}%")
            ->unionAll(
                DB::table('artists')
                    ->select('id', 'name','created_at', DB::raw("'artist' as type"))
                    ->where('name', 'like', "%{$search}%")
            )
            ->unionAll(
                DB::table('playlists')
                    ->select('id', 'name','created_at', DB::raw("'playlist' as type"))
                    ->where('name', 'like', "%{$search}%")
                    ->where('type',  '=', 2)
            );
    }

    private function getResultsTypeTrack($search) {
        return DB::table('tracks')
            ->select('id', 'name','created_at', DB::raw("'track' as type"))
            ->where('name', 'like', "%{$search}%")
            ->orderBy('created_at');
    }

    private function getResultsTypePlaylist($search) {
        return DB::table('playlists')
            ->select('id', 'name','created_at', DB::raw("'playlist' as type"))
            ->where('name', 'like', "%{$search}%")
            ->where('type',  '=', 2)
            ->orderBy('created_at');
    }

    private function getResultsTypeArtist($search) {
        return DB::table('artists')
            ->select('id', 'name','created_at', DB::raw("'artist' as type"))
            ->where('name', 'like', "%{$search}%")
            ->orderBy('created_at');
    }

    public function getTracks(array $tracks) {
        $ids = array_map(function ($item) {
            return $item->id;
        }, $tracks);
        $tracks = Track::whereIn('id', $ids)->get();
        return TrackSearchResource::collection($tracks);
    }

    public function getArtists(array $artists) {
        $ids = array_map(function ($item) {
            return $item->id;
        }, $artists);
        $artists = Artist::whereIn('id', $ids)->get();
        return ArtistsSearchResource::collection($artists);
    }

    public function getPlaylists(array $playlists) {
        $ids = array_map(function ($item) {
            return $item->id;
        }, $playlists);
        $playlists = Playlist::whereIn('id', $ids)->get();
        return PlaylistSearchResource::collection($playlists);
    }

}

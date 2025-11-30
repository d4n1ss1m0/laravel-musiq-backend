<?php

namespace App\Service\AddTrack;

use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Domain\Entities\Artist;
use App\Domain\Entities\Track;
use App\Domain\Entities\TrackArtists;
use App\Service\Auth\AccountService;
use App\Service\FileService\FileService;
use App\Shared\Traits\HttpResponse;
use getID3;

class AddTrackService implements AddTrackServiceInterface
{
    use HttpResponse;
    public function __construct(
        protected readonly AccountService $accountService,
        protected readonly FileService $fileService
    ){}


    public function createTrack($name, $time, $song, $image) {
        $songPath = $song;
        //$songPath = explode('/', $song)[1];
        $imagePathArray = explode('/', $image);
        $imagePath = "{$imagePathArray[1]}/{$imagePathArray[2]}";
        $track = Track::create([
            'name' => $name,
            'time' => $time,
            'song' => $songPath,
            'image' => $imagePath,
            'is_private' => false,
            'user_id' => $this->accountService->getAccountId(),
        ]);
        return $track;
    }

    public function addArtistsToTrack(array $artists, int $trackId) {
        $trackArtistsArray = array_map(function ($artist) use ($trackId) {
            return [
                'track_id' => $trackId,
                'artist_id' => $artist['id']
            ];
        }, $artists);

        TrackArtists::insert($trackArtistsArray);
    }


    public function createArtist(array $artist, string $cover) {
        $imagePathArray = explode('/', $cover);
        $imagePath = "{$imagePathArray[1]}/{$imagePathArray[2]}";
        return Artist::create([
            'name' => $artist['name'],
            'image' => $imagePath
        ]);
    }

}

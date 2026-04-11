<?php

namespace App\Service\TrackService;

use App\DTO\AddTrack\AddTrackDTO;
use App\Enum\MusicService;
use App\Models\Track;

interface TrackServiceInterface
{
    public function addTrackByFile(AddTrackDTO $dto, int $userId);
    public function addArtistsToTrack(Track $track, array $artists);
    public function parseFromUrl(string $url, MusicService $musicService);
}

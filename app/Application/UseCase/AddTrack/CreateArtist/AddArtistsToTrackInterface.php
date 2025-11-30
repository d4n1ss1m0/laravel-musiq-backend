<?php

namespace App\Application\UseCase\AddTrack\CreateArtist;

use App\Application\DTO\AddTrack\AddTrackDTO;
use App\Domain\Models\Track;

interface AddArtistsToTrackInterface
{
    public function handle(Track $track, array $artists);
}

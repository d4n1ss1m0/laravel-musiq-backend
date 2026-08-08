<?php

namespace App\Service\PlaybackService;

use App\DTO\AddTrack\AddTrackLinkDTO;
use App\Enum\MusicService;
use App\Models\Track;
use App\DTO\AddTrack\AddTrackDTO;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use getID3;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Process;

class PlaybackService implements PlaybackServiceInterface
{

}

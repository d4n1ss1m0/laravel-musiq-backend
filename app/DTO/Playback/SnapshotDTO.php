<?php

namespace App\DTO\Playback;

use App\DTO\ArtistDTO;
use App\Enum\PlaybackSource;
use App\Enum\RepeatType;
use Illuminate\Http\UploadedFile;

class SnapshotDTO
{
    public PlaybackSource $source;
    public string $sourceId;
    public string $trackId;
    public RepeatType $repeatType;
    public bool $shuffle;

    public function __construct(PlaybackSource $source, string $sourceId, string $trackId, RepeatType $repeatType, bool $shuffle)
    {
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->trackId = $trackId;
        $this->repeatType = $repeatType;
        $this->shuffle = $shuffle;
    }
}

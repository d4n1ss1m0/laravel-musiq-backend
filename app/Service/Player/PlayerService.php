<?php

namespace App\Service\Player;

use App\Models\Track;
use App\Service\MusicStream\MusicStreamServiceInterface;

class PlayerService
{

    public function __construct(private readonly MusicStreamServiceInterface $musicStreamService)
    {
    }

    function streamData(string $id, ?int $rangeFrom, ?int $rangeTo)
    {
        $song = Track::where('uuid', $id)->value('song');
        $filePath = storage_path("app/audio/{$song}");
        [$callback, $fileSize, $start, $end] = $this->musicStreamService->stream($filePath, $rangeFrom, $rangeTo);
        return [$filePath, $fileSize, $callback, $start, $end];
    }
}

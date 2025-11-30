<?php

namespace App\Application\UseCase\Player\MusicStream;

use App\Domain\Models\Track;
use App\Infrastructure\Services\MusicStream\MusicStreamServiceInterface;

class MusicStream implements MusicStreamInterface
{
    public function __construct(private readonly MusicStreamServiceInterface $musicStreamService)
    {
    }

    public function handle(int $id, ?int $rangeFrom, ?int $rangeTo): array
    {
        $song = Track::where('id', $id)->value('song');
        $filePath = storage_path("app/{$song}");
        [$callback, $fileSize, $start, $end] = $this->musicStreamService->stream($filePath, $rangeFrom, $rangeTo);
        return [$filePath, $fileSize, $callback, $start, $end];
    }
}

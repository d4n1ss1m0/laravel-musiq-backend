<?php

namespace App\Application\UseCase\AddTrack\AddTrackByFile;

use App\Application\DTO\AddTrack\AddTrackDTO;

interface AddTrackByFileInterface
{
    public function handle(AddTrackDTO $dto, int $userId);
}

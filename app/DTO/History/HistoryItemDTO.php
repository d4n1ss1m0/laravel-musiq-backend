<?php

namespace App\DTO\History;

use App\Enum\HistorySource;
use App\Shared\Enums\PlaylistTypes;

class HistoryItemDTO
{
//    public int $id;
    public int $userId;
    public int $trackId;
    public string|null $sourceType;
    public int|null $sourceId;

    public function __construct(int $userId, int $trackId, string|null $sourceType = null, int|null $sourceId = null)
    {
        $this->userId = $userId;
        $this->trackId = $trackId;
        $this->sourceType = $sourceType;
        $this->sourceId = $sourceId;
    }
}

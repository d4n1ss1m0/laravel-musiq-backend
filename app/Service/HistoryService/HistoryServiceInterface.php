<?php

namespace App\Service\HistoryService;

use App\Enum\HistorySource;

interface HistoryServiceInterface
{
    public function store(int $userId, string $trackId, $sourceId, HistorySource $sourceType): void;
}

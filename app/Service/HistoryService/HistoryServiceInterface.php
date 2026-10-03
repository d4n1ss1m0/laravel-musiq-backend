<?php

namespace App\Service\HistoryService;

use App\DTO\History\ListenHistoryDTO;
use App\Enum\HistorySource;

interface HistoryServiceInterface
{
    public function store(int $userId, string $trackId, $sourceId, ?HistorySource $sourceType): void;

    public function listen(ListenHistoryDTO $dto): array;

    public function get(int $userId, int $limit, int $page): array;
}

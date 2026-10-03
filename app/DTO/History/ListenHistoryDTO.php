<?php

namespace App\DTO\History;

use App\Enum\HistorySource;
use Carbon\CarbonImmutable;

class ListenHistoryDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $attemptId,
        public readonly string $trackId,
        public readonly ?HistorySource $source,
        public readonly ?string $sourceId,
        public readonly CarbonImmutable $startedAt,
        public readonly int $listenedSeconds,
        public readonly bool $finished = false,
    ) {}
}

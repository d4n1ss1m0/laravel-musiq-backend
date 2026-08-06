<?php

namespace App\Repositories\History;



use App\DTO\History\HistoryItemDTO;
use App\Models\ListeningHistoryItem;
use Carbon\Carbon;

interface HistoryRepositoryInterface
{
    public function store(HistoryItemDTO $historyItemDTO): ListeningHistoryItem;
    public function get(HistoryItemDTO $historyItemDTO, Carbon $date): ?ListeningHistoryItem;
    public function incrementListen(ListeningHistoryItem $listenHistoryItem): void;
}

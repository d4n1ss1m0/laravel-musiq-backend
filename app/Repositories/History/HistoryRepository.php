<?php

namespace App\Repositories\History;

use App\DTO\History\HistoryItemDTO;
use App\Models\Artist;
use App\Models\ListeningHistoryItem;
use App\Repositories\Artist\ArtistRepositoryInterface;
use Carbon\Carbon;

class HistoryRepository implements HistoryRepositoryInterface
{


    public function store(HistoryItemDTO $historyItemDTO): ListeningHistoryItem
    {
        $date = Carbon::now();
        return ListeningHistoryItem::create([
            'user_id' => $historyItemDTO->userId,
            'track_id' => $historyItemDTO->trackId,
            'source_type' => $historyItemDTO->sourceType,
            'source_id' => $historyItemDTO->sourceId,
            'played_date' => $date->toDateTimeString(),
            'last_played_at' => $date,
            'play_count' => 1
        ]);
    }


    public function get(HistoryItemDTO $historyItemDTO, Carbon $date): ?ListeningHistoryItem
    {
        return ListeningHistoryItem::query()
            ->where('user_id', $historyItemDTO->userId)
            ->where('track_id', $historyItemDTO->trackId)
            ->where('source_type', $historyItemDTO->sourceType)
            ->where('source_id', $historyItemDTO->sourceId)
            ->where('played_date', $date->toDateString())
            ->first();
    }

    public function incrementListen(ListeningHistoryItem $listenHistoryItem): void
    {
        $listenHistoryItem->last_played_at = Carbon::now();
        $listenHistoryItem->play_count++;
        $listenHistoryItem->save();
    }
}

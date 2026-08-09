<?php

namespace App\Http\Resources\Playback;

use App\Enum\PlaybackSource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaybackSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'source' => [
                'sourceType' => PlaybackSource::getByClassName(get_class($this->source)),
                'sourceId' => $this->source->uuid
            ],
            'currentTrackId' => $this->currentTrack->uuid,
            'currentPosition' => $this->current_position,
            'prev' => $this->previousQueueItem()->track->uuid,
            'next' => $this->nextQueueItem()->track->uuid,
            'shuffle' => $this->shuffle,
            'repeateMode' => $this->repeat_mode->value,
            'state' => $this->state->value
        ];
    }
}

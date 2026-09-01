<?php

namespace App\Http\Resources\Playback;

use App\Http\Resources\Tracks\TrackResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaybackTrackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'track' => new TrackResource($this->resource->track),
            'playbackPosition' => $this->resource->playback_position,
        ];
    }
}

<?php

namespace App\Http\Resources\Tracks;

use App\Http\Resources\Artist\ArtistsResource;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrackSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'cover' => $this->image ?'/image/'.$this->image : '',
            'song' => $this->song,
            'artists' => ArtistsResource::collection($this->artists),
            'time' => $this->time,
            'type' => 'track'
        ];
    }

    public function secondsToString(int $seconds) {
        $interval = CarbonInterval::seconds($seconds)->cascade();

        if ($interval->hours > 0) {
            return $interval->format('%h:%I:%S');
        }

        return $interval->format('%i:%S');
    }
}

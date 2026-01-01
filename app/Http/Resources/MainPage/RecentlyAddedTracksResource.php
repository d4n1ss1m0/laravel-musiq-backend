<?php

namespace App\Http\Resources\MainPage;

use App\Http\Resources\Artist\ArtistsResource;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RecentlyAddedTracksResource extends JsonResource
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
            'artists' => ArtistsResource::collection($this->artists),
            'song' => substr($this->song, 0, strrpos($this->song, '.')),
            'time' => $this->time,
            'isFavourite' => false
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

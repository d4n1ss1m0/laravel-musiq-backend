<?php

namespace App\Http\Resources\MainPage;

use App\Http\Resources\Artist\ArtistsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RecentlyPlayedTracksResource extends JsonResource
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
            'song' => substr($this->song, 0, strrpos($this->song, '.')),
            'artists' => ArtistsResource::collection($this->artists)
        ];
    }
}

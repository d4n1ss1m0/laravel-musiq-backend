<?php

namespace App\Http\Resources\Playlists;

use App\Http\Resources\ArtistsResource;
use App\Http\Resources\TrackResource;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaylistResource extends JsonResource
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
            'image' => $this->image ?'/image/playlist/'.$this->image : '',
            'type' => $this->playlistType->name,
            //'tracks' => TrackResource::collection($this->tracks)
        ];
    }

}

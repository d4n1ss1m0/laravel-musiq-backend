<?php

namespace App\Http\Resources\MainPage;

use App\Http\Resources\ArtistsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RecentlyPlayedPlaylistsResource extends JsonResource
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
        ];
    }
}

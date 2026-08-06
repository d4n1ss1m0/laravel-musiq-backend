<?php

namespace App\Http\Resources\Mediateka;

use App\Http\Resources\Artist\ArtistsResource;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Models\Artist;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MediatekaItemResource extends JsonResource
{
    const ITEM_TYPES = [
        Playlist::class => PlaylistResource::class,
        Artist::class => ArtistsResource::class
    ];
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = self::ITEM_TYPES[$this->libraryable_type] ?? null;

        if (!$resource) {
            throw new \Exception('Unsupported mediateka item type');
        }

        return [
            'type' => class_basename($this->libraryable_type),
            'item' => new $resource($this->libraryable),
        ];
    }
}

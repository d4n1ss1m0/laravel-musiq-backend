<?php

namespace App\Http\Resources\Playlists;

use App\Http\Resources\ArtistsResource;
use App\Http\Resources\TrackResource;
use App\Models\Playlist;
use App\Shared\Enums\PlaylistTypes;
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
        $imagesArray = [];
        if ($this->type !== PlaylistTypes::FAVOURITE->getId()) {
            if ($this->image != null) {
                $imagesArray[] = '/image/playlist/'.$this->image;
            } else {
                $tracksWithImages = $this->tracks()
                    ->whereNotNull('tracks.image')
                    ->where('tracks.image', '!=', '')
                    ->orderBy('track_playlists.order')
                    ->limit(4)
                    ->get(['tracks.image'])
                    ->pluck(['image'])
                    ->toArray();

                if (count($tracksWithImages) > 0) {

                    foreach ($tracksWithImages as $image) {
                        $imagesArray[] = '/image/track/'.$image;
                    }

                    $lenght = count($imagesArray) < 4 ? 1 : 4;

                    $imagesArray = array_slice($imagesArray, 0, $lenght);
                }
            }
        }


        return[
            'id' => $this->uuid,
            'name' => $this->name,
//            'image' => $this->image ?'/image/playlist/'.$this->image : '',
            'image' => $imagesArray,
            'type' => $this->playlistType->name,
            //'tracks' => TrackResource::collection($this->tracks)
        ];
    }

}

<?php

namespace App\Http\Resources\Playlists;

use App\Exceptions\ApiException;
use App\Http\Resources\ArtistsResource;
use App\Http\Resources\TrackResource;
use App\Models\Playlist;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaylistResource extends JsonResource
{
    private bool $withTracksStats = false;
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

        $result = [
            'id' => $this->uuid,
            'name' => $this->name,
            'image' => $imagesArray,
            'type' => [
                'id' => $this->playlistType->id,
                'name' => $this->playlistType->name
            ],
            'isOwner' => (int) $this->user_id === (int) $request->attributes->get(Fields::USER_ID),
            //'tracks' => TrackResource::collection($this->tracks)
        ];

        if ($this->withTracksStats) {
            $this->tracksStats($result);
        }

        return $result;
    }

    private function tracksStats(&$result): void
    {
        $attributes = $this->resource->getAttributes();

        if (!array_key_exists('tracks_count', $attributes)
            || !array_key_exists('tracks_duration', $attributes)) {
            throw new \LogicException('Playlist track statistics were not loaded');
        }

        $result['tracks'] = [
            'count' => (int) $this->tracks_count,
            'duration' => (int) ($this->tracks_duration ?? 0)
        ];
    }

    public function withTracksStats()
    {
        $this->withTracksStats = true;

        return $this;
    }

}

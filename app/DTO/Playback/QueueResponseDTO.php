<?php

namespace App\DTO\Playback;

use App\Http\Resources\Playback\PlaybackSessionResource;
use App\Http\Resources\Playback\PlaybackTrackResource;
use App\Models\PlaybackSession\PlaybackSession;
use App\Shared\Fields\Fields;
use Illuminate\Pagination\LengthAwarePaginator;

class QueueResponseDTO
{
    public LengthAwarePaginator $tracks;
    public PlaybackSession $session;

    public function __construct(LengthAwarePaginator $tracks, PlaybackSession $session)
    {
        $this->tracks = $tracks;
        $this->session = $session;
    }

    public function getResponse() : array
    {
        return [
            Fields::ITEMS => PlaybackTrackResource::collection($this->tracks),
            Fields::SESSION => new PlaybackSessionResource($this->session),
        ];
    }
}

<?php

namespace App\Service\MainPage\RecentlyAddedTracks;

use App\Http\Resources\Tracks\TrackResource;
use App\Models\Track;

class RecentlyAddedTracksService implements RecentlyAddedTracksServiceInterface
{

    public function getRecently() {
        $tracks = Track::limit(10)
            ->with('artists')
            ->orderByDesc('created_at')
            ->get()
            ->keyBy('uuid');

        return [
            'trackIds' => array_keys($tracks->all()),
            'tracks' => TrackResource::collection(array_values($tracks->all())),
        ];

    }
}

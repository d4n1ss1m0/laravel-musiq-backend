<?php

namespace App\Service\YearReviewService\modules;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\PlaylistTrackDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\DTO\YearReview\YearReviewModuleDTO;
use App\Exceptions\NotFoundException;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Mediateka\MediatekaRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\PlaylistService\PlaylistServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use App\Service\YearReviewService\YearReviewServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TopArtistsModule implements YearReviewServiceModuleInterface
{

    public static function execute(YearReviewModuleDTO $context, ?array $options = []): array
    {
        $artists = DB::table('listening_history as lh')
            ->join('tracks as t', 't.id', '=', 'lh.track_id')
            ->join('track_artists as ta', 'ta.track_id', '=', 't.id')
            ->join('artists as a', 'a.id', '=', 'ta.artist_id')
            ->where('lh.user_id', $context->userId)
            ->whereYear('lh.played_date', $context->year)
            ->groupBy('a.id', 'a.uuid', 'a.name', 'a.image')
            ->select([ 'a.id', 'a.uuid', 'a.name', 'a.image', ])
            ->selectRaw(
                'SUM(lh.play_count * LEAST(t.time, 300)) AS total_seconds'
            )
            ->selectRaw('ROUND(
                SUM(lh.play_count * LEAST(t.time, 300)) / 60.0) AS total_min'
            )
            ->selectRaw('SUM(lh.play_count) AS play_count')
            ->selectRaw('COUNT(DISTINCT t.id) AS unique_tracks')
            ->orderByDesc('total_seconds')
            ->orderByDesc('play_count')
            ->orderByDesc('unique_tracks')
            ->limit($options['limit'] ?? 5)
            ->get();
        $artists->transform(function ($artist) use ($context) {
            return json_decode(json_encode($artist), true);
        });

        return [
            'value' => $artists->toArray(),
        ];
    }
}

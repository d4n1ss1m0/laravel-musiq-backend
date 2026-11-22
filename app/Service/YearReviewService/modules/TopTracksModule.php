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

class TopTracksModule implements YearReviewServiceModuleInterface
{

    public static function execute(YearReviewModuleDTO $context, ?array $options = []): array
    {
        $tracks = DB::table('listening_history as lh')
            ->join('tracks as t', 't.id', '=', 'lh.track_id')
            ->where('lh.user_id', $context->userId)
            ->whereYear('lh.played_date', $context->year)
            ->groupBy('t.id', 't.uuid', 't.name', 't.image', 't.time')
            ->select([
                't.id',
                't.uuid',
                't.name',
                't.image',
                't.time',
            ])
            ->selectRaw('SUM(lh.play_count) AS play_count')
            ->selectRaw(
                'SUM(lh.play_count * LEAST(t.time, 300)) AS total_seconds'
            )
            ->orderByDesc('play_count')
            ->orderByDesc('total_seconds')
            ->orderBy('t.id')
            ->limit($options['limit'] ?? 5)
            ->get();

        $trackIds = $tracks->pluck('id');

        $artistsByTrack = DB::table('track_artists as ta')
            ->join('artists as a', 'a.id', '=', 'ta.artist_id')
            ->whereIn('ta.track_id', $trackIds)
            ->orderBy('ta.id')
            ->get([
                'ta.track_id',
                'a.uuid',
                'a.name',
                'a.image',
            ])
            ->groupBy('track_id');

        $tracks->transform(function ($track) use ($artistsByTrack) {
            $track->artists = $artistsByTrack
                ->get($track->id, collect())
                ->map(fn ($artist) => [
                    'id' => $artist->uuid,
                    'name' => $artist->name,
                    'image' => $artist->image,
                ])
                ->values()
                ->all();

            $track->id = $track->uuid;
            unset($track->uuid);

            return json_decode(json_encode($track), true);
        });

        return [
            'value' => $tracks->toArray(),
        ];
    }
}

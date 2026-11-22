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

class TotalMinutesModule implements YearReviewServiceModuleInterface
{

    public static function execute(YearReviewModuleDTO $context, ?array $options = []): array
    {
        $totalSec = DB::table('listening_history as lh')
            ->join('tracks as t', 'lh.track_id', '=', 't.id')
            ->where('lh.user_id', $context->userId)
            ->whereYear('lh.played_date', $context->year)
            ->selectRaw('SUM(lh.play_count * LEAST(t.time, 300)) as total_sec')
            ->value('total_sec');

        $totalMin = round($totalSec / 60);
        $totalDay = floor($totalMin/60/24);

        return [
            'value' => [
                "min" => (string)$totalMin,
                "day" => (string)$totalDay
            ],
        ];
    }
}

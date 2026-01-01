<?php

namespace App\Listeners;

use App\Events\TrackPlayed;
use App\Models\RecentlyPlayedTrack;
use App\Models\Track;
use App\Repositories\RecentlyPlayedTrack\RecentlyPlayedTrackRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTrackNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TrackPlayed $event): void
    {
        $trackId = Track::query()
            ->where('uuid', $event->trackId)
            ->first('id')->value('id');
        $recentlyPlayedTrackTodayExists = RecentlyPlayedTrack::query()
            ->where('user_id', $event->userId)
            ->where('track_id', $trackId)
            ->where('created_at','>', Carbon::now()->startOfDay()->toDateTimeString())
            ->exists();

        if (!$recentlyPlayedTrackTodayExists) {
            $rep = app(RecentlyPlayedTrackRepositoryInterface::class);
            $rep->create($event->userId, $trackId);
        }
    }
}

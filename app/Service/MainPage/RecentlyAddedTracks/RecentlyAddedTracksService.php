<?php

namespace App\Service\MainPage\RecentlyAddedTracks;

use App\Models\Track;
use Illuminate\Pagination\LengthAwarePaginator;

class RecentlyAddedTracksService implements RecentlyAddedTracksServiceInterface
{

    public function getRecently(int $perPage = 10): LengthAwarePaginator {
        $paginator = Track::query()
            ->with('artists')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $paginator;
    }
}

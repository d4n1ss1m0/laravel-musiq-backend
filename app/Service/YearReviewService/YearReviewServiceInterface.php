<?php

namespace App\Service\YearReviewService;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Models\Playlist;

interface YearReviewServiceInterface
{
    public function generateReview(int $userId, int $year):array;
}

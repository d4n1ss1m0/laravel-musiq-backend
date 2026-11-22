<?php

namespace App\Service\YearReviewService\modules;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\DTO\YearReview\YearReviewModuleDTO;
use App\Models\Playlist;

interface YearReviewServiceModuleInterface
{
    public static function execute(YearReviewModuleDTO $context, ?array $options = []):array;
}

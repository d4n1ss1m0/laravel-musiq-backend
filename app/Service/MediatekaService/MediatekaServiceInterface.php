<?php

namespace App\Service\MediatekaService;

use App\Enum\OrderBy;

interface MediatekaServiceInterface
{
    public function getMediateka(int $userId, OrderBy $orderBy = OrderBy::RECENT, string $query = '');
    public function addPlaylist(int $userId, int $playlistId);
}

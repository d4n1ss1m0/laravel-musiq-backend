<?php

namespace App\Service\MediatekaService;

use App\Enum\MediatekaItemType;
use App\Enum\OrderBy;

interface MediatekaServiceInterface
{
    public function getMediateka(int $userId, OrderBy $orderBy = OrderBy::RECENT, string $query = '');
    public function addMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId);
    public function removeMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId);

    public function pinMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId);
    public function unpinMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId);
}

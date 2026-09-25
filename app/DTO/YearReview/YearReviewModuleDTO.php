<?php

namespace App\DTO\YearReview;

class YearReviewModuleDTO
{
    public function __construct(
        public int $userId,
        public int $year,

    )
    {
    }
}

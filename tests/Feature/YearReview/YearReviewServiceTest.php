<?php

namespace Tests\Feature\YearReview;

use App\DTO\YearReview\YearReviewModuleDTO;
use App\Service\YearReviewService\YearReviewService;
use App\Shared\Enums\YearReviewBlocks;
use Tests\TestCase;

class YearReviewServiceTest extends TestCase
{
    public function test_pages_with_missing_dataset_items_are_omitted(): void
    {
        config()->set('year_review.2026', [
            'datasets' => [
                'artists' => ['module' => EmptyArtistsModule::class, 'options' => []],
            ],
            'pages' => [
                [
                    'page' => 1,
                    'background' => 'intro.webp',
                    'blocks' => [['type' => YearReviewBlocks::TITLE, 'value' => 'Intro']],
                ],
                [
                    'page' => 2,
                    'background' => 'artist.webp',
                    'blocks' => [
                        ['type' => YearReviewBlocks::TITLE, 'value' => 'Favourite artist'],
                        ['type' => YearReviewBlocks::ARTIST, 'dataset' => 'artists', 'index' => 0, 'value' => '{value}'],
                    ],
                ],
                [
                    'page' => 3,
                    'background' => 'artists.webp',
                    'blocks' => [
                        ['type' => YearReviewBlocks::LIST, 'valueType' => YearReviewBlocks::ARTIST, 'dataset' => 'artists', 'value' => '{value}'],
                    ],
                ],
            ],
        ]);

        $review = (new YearReviewService)->generateReview(42, 2026);

        $this->assertSame([1], array_column($review['pages'], 'page'));
        $this->assertSame([['type' => 'title', 'value' => 'Intro']], $review['pages'][0]['blocks']);
    }
}

class EmptyArtistsModule
{
    public static function execute(YearReviewModuleDTO $context, array $options): array
    {
        return ['value' => []];
    }
}

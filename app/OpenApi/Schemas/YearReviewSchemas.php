<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'YearReviewTrack',
    description: 'Track embedded in a year review. Uses image rather than the general Track resource cover field.',
    required: ['id', 'name', 'image', 'artists'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'name', type: 'string', example: 'Around the World'),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'track/cover.webp'),
        new OA\Property(property: 'artists', type: 'array', items: new OA\Items(ref: '#/components/schemas/Artist')),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'YearReviewBlock',
    description: 'Block content depends on type: title/text contain text or a number, artist contains an Artist, track contains a YearReviewTrack, and list contains typed blocks for its items.',
    required: ['type', 'value'],
    properties: [
        new OA\Property(property: 'type', type: 'string', enum: ['title', 'text', 'artist', 'track', 'list'], example: 'title'),
        new OA\Property(property: 'value', anyOf: [
            new OA\Schema(type: 'string', example: 'Твои итоги года'),
            new OA\Schema(type: 'number', example: 120),
            new OA\Schema(ref: '#/components/schemas/Artist'),
            new OA\Schema(ref: '#/components/schemas/YearReviewTrack'),
            new OA\Schema(type: 'array', items: new OA\Items(ref: '#/components/schemas/YearReviewBlock')),
        ]),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'YearReviewPage',
    required: ['page', 'background', 'blocks'],
    properties: [
        new OA\Property(property: 'page', type: 'integer', example: 1),
        new OA\Property(property: 'background', type: 'string', example: 'year_review/background/2026/background1.webp'),
        new OA\Property(property: 'blocks', type: 'array', items: new OA\Items(ref: '#/components/schemas/YearReviewBlock')),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'YearReviewPreview',
    description: 'First review page enriched with year and viewed. If a stored review contains no pages, only year and viewed are returned.',
    required: ['year', 'viewed'],
    properties: [
        new OA\Property(property: 'year', type: 'integer', example: 2026),
        new OA\Property(property: 'viewed', type: 'boolean', example: false),
        new OA\Property(property: 'page', type: 'integer', example: 1),
        new OA\Property(property: 'background', type: 'string', example: 'year_review/background/2026/background1.webp'),
        new OA\Property(property: 'blocks', type: 'array', items: new OA\Items(ref: '#/components/schemas/YearReviewBlock')),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'YearReview',
    required: ['year', 'user_id', 'pages'],
    example: YearReviewSchemas::EXAMPLE,
    properties: [
        new OA\Property(property: 'year', type: 'integer', example: 2026),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'pages', type: 'array', items: new OA\Items(ref: '#/components/schemas/YearReviewPage')),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'YearReviewListData',
    required: ['items', 'pagination'],
    properties: [
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/YearReviewPreview')),
        new OA\Property(property: 'pagination', ref: '#/components/schemas/Pagination'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'YearReviewValidationErrorResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')),
            example: ['limit' => ['Количество элементов на странице должно быть не меньше 1.']],
        ),
        new OA\Property(property: 'status', type: 'string', example: 'error'),
        new OA\Property(property: 'code', type: 'integer', example: 422),
    ],
    type: 'object',
)]
final class YearReviewSchemas
{
    public const EXAMPLE = [
        'year' => 2026,
        'user_id' => 3,
        'pages' => [
            [
                'page' => 1,
                'background' => 'year_review/background/2026/background1.webp',
                'blocks' => [
                    ['type' => 'title', 'value' => 'Твои итоги года'],
                    ['type' => 'text', 'value' => 'Самое время оглянуться назад. Давай посмотрим, чем тебе запомнился этот год в музыке.'],
                ],
            ],
            [
                'page' => 2,
                'background' => 'year_review/background/2026/background1.webp',
                'blocks' => [
                    ['type' => 'title', 'value' => 'Прослушано минут'],
                    ['type' => 'text', 'value' => '0'],
                    ['type' => 'text', 'value' => 'это 0 дней'],
                    ['type' => 'text', 'value' => 'Впечатляющий результат! А теперь узнаем, кто из исполнителей звучал у тебя чаще остальных.'],
                ],
            ],
        ],
    ];
}

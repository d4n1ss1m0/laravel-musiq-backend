<?php

namespace App\OpenApi\Paths;

use App\OpenApi\Schemas\YearReviewSchemas;
use OpenApi\Attributes as OA;

class YearReviewPaths
{
    #[OA\Get(
        path: '/personal/year-reviews',
        summary: 'Get available year review previews',
        description: 'Returns only the current user’s successful reviews whose available_from date has arrived, ordered by year descending. Each item contains the first page, year and viewed flag. Does not mark reviews as viewed.',
        security: [['tokenAuth' => []]],
        tags: ['Year reviews'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Page size. Default 10, range 1–50 unless overridden by application pagination settings.',
                schema: new OA\Schema(type: 'integer', example: 10, minimum: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated previews; items is an empty array when no reviews are available.',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                    new OA\Schema(properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/YearReviewListData'),
                    ], type: 'object'),
                ]),
            ),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/PlainErrorResponse')),
            new OA\Response(response: 422, description: 'Invalid page or limit', content: new OA\JsonContent(ref: '#/components/schemas/YearReviewValidationErrorResponse')),
        ],
    )]
    public function getReviews(): void {}

    #[OA\Get(
        path: '/personal/year-review/new',
        summary: 'Get an unread year review preview',
        description: 'Returns the first page of the newest available, successful, unread review belonging to the current user, with year and viewed=false. Returns data=null when none exists. Does not mark it as viewed; open the full review to mark it as viewed.',
        security: [['tokenAuth' => []]],
        tags: ['Year reviews'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Unread review preview or null',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                    new OA\Schema(properties: [
                        new OA\Property(property: 'data', oneOf: [
                            new OA\Schema(ref: '#/components/schemas/YearReviewPreview'),
                            new OA\Schema(type: 'object', nullable: true, enum: [null]),
                        ]),
                    ], type: 'object'),
                ]),
            ),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/PlainErrorResponse')),
        ],
    )]
    public function getNewReview(): void {}

    #[OA\Get(
        path: '/personal/year-review/{year}',
        summary: 'Get a full year review and mark it as viewed',
        description: 'Returns all generated pages of the current user’s successful review after its available_from date. Opening the review sets viewed=true, so it is no longer returned by /personal/year-review/new. Pages without statistics can be omitted; page numbers need not be consecutive.',
        security: [['tokenAuth' => []]],
        tags: ['Year reviews'],
        parameters: [
            new OA\Parameter(
                name: 'year',
                in: 'path',
                required: true,
                description: 'Four-digit review year',
                schema: new OA\Schema(type: 'integer', example: 2026, maximum: 9999, minimum: 0),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Full review; viewed flag is stored on the review and exposed by the preview endpoints.',
                content: new OA\JsonContent(example: [
                    'data' => YearReviewSchemas::EXAMPLE,
                    'status' => 'success',
                    'code' => 200,
                ], allOf: [
                    new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                    new OA\Schema(properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/YearReview'),
                    ], type: 'object'),
                ]),
            ),
            new OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent(ref: '#/components/schemas/PlainErrorResponse')),
            new OA\Response(
                response: 404,
                description: 'No available successful review for this user and year, or invalid year route format.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ApiErrorResponse',
                    example: ['data' => 'Отчет года не найден', 'status' => 'error', 'code' => 404],
                ),
            ),
        ],
    )]
    public function getReviewByYear(): void {}
}

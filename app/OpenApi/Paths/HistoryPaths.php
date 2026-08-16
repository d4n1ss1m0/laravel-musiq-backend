<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class HistoryPaths
{
    #[OA\Post(
        path: '/history',
        summary: 'Add track to listening history',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/HistoryStoreRequest'),
        ),
        tags: ['History'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'History item stored',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', type: 'string', example: 'success'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function store(): void
    {
    }
}

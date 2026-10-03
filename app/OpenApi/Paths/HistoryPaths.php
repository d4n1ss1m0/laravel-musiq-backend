<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class HistoryPaths
{
    #[OA\Post(
        path: '/history',
        summary: 'Add track to listening history (legacy)',
        deprecated: true,
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/HistoryStoreRequest'),
        ),
        tags: ['History'],
        responses: [
            new OA\Response(response: 200, description: 'History item stored'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Track or source not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function store(): void {}

    #[OA\Post(
        path: '/history/listen',
        summary: 'Synchronize cumulative listening progress',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/HistoryListenRequest'),
        ),
        tags: ['History'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listening progress synchronized',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryListenResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Track or source not found'),
            new OA\Response(response: 409, description: 'Attempt data conflict'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function listen(): void {}
}

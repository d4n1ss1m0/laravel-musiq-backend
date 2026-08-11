<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class AddTrackPaths
{
    #[OA\Post(
        path: '/add-track',
        summary: 'Add track by uploaded file',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/AddTrackByFileRequest'),
            ),
        ),
        tags: ['Add Track'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Track added',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/AddTrackResult'),
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
    public function addTrackByFile(): void
    {
    }

    #[OA\Post(
        path: '/add-track/parse',
        summary: 'Parse track metadata from link',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ParseTrackLinkRequest'),
        ),
        tags: ['Add Track'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Parsed track metadata',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/ParseTrackLinkResult'),
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
    public function parseFromLink(): void
    {
    }

    #[OA\Post(
        path: '/add-track/parse/after-parse',
        summary: 'Add track after parsing link',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/AddTrackAfterParseRequest'),
            ),
        ),
        tags: ['Add Track'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Track added',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/AddTrackResult'),
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
    public function addAfterParse(): void
    {
    }
}

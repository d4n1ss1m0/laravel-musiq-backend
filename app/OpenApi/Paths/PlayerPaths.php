<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class PlayerPaths
{
    #[OA\Get(
        path: '/tracks',
        summary: 'Get tracks by UUIDs',
        tags: ['Player'],
        parameters: [
            new OA\Parameter(
                name: 'ids',
                description: 'Comma-separated track UUIDs',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd,018ff4e6-5d84-7000-8e14-2f6d17c1b9fe'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tracks',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlayerTracksData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function getTracks(): void
    {
    }

    #[OA\Get(
        path: '/track/{uuid}',
        summary: 'Get track',
        tags: ['Player'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Track UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Track',
                content: new OA\JsonContent(ref: '#/components/schemas/PlayerTrackResponse'),
            ),
            new OA\Response(response: 404, description: 'Track not found'),
        ],
    )]
    public function getTrack(): void
    {
    }

    #[OA\Get(
        path: '/stream/{id}',
        summary: 'Stream track audio',
        security: [['tokenAuth' => []]],
        tags: ['Player'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Track UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
            new OA\Parameter(
                name: 'Range',
                description: 'Byte range header',
                in: 'header',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'bytes=0-1023'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 206,
                description: 'Audio stream',
                headers: [
                    new OA\Header(
                        header: 'Accept-Ranges',
                        description: 'Accepted range unit',
                        schema: new OA\Schema(type: 'string', example: 'bytes'),
                    ),
                    new OA\Header(
                        header: 'Content-Range',
                        description: 'Returned byte range',
                        schema: new OA\Schema(type: 'string', example: 'bytes 0-1023/5242880'),
                    ),
                    new OA\Header(
                        header: 'Content-Length',
                        description: 'Response body size in bytes',
                        schema: new OA\Schema(type: 'integer', example: 1024),
                    ),
                ],
                content: new OA\MediaType(
                    mediaType: 'audio/ogg',
                    schema: new OA\Schema(type: 'string', format: 'binary'),
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Audio file not found'),
            new OA\Response(response: 416, description: 'Requested range not satisfiable'),
        ],
    )]
    public function stream(): void
    {
    }
}

<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class ImageServicePaths
{
    #[OA\Get(
        path: '/image/{type}/{name}',
        summary: 'Get image',
        tags: ['Image Service'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Image directory type',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['track', 'artist', 'playlist', 'tmp'], example: 'track'),
            ),
            new OA\Parameter(
                name: 'name',
                description: 'Image file name',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd.webp'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Image file',
                content: [
                    new OA\MediaType(
                        mediaType: 'image/webp',
                        schema: new OA\Schema(type: 'string', format: 'binary'),
                    ),
                    new OA\MediaType(
                        mediaType: 'image/png',
                        schema: new OA\Schema(type: 'string', format: 'binary'),
                    ),
                    new OA\MediaType(
                        mediaType: 'image/jpeg',
                        schema: new OA\Schema(type: 'string', format: 'binary'),
                    ),
                ],
            ),
            new OA\Response(
                response: 500,
                description: 'Image not found or cannot be read',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function getImage(): void
    {
    }
}

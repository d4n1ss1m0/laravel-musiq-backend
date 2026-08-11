<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class ArtistPaths
{
    #[OA\Get(
        path: '/artist/{uuid}',
        summary: 'Get artist',
        tags: ['Artist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Artist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Artist',
                content: new OA\JsonContent(ref: '#/components/schemas/ArtistResourceResponse'),
            ),
            new OA\Response(response: 404, description: 'Artist not found'),
        ],
    )]
    public function getArtist(): void
    {
    }

    #[OA\Get(
        path: '/artists/search',
        summary: 'Search artists',
        tags: ['Artist'],
        parameters: [
            new OA\Parameter(
                name: 'query',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', maxLength: 255),
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1),
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Artists',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/ArtistsSearchPaginatedData'),
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
    public function searchArtists(): void
    {
    }
}

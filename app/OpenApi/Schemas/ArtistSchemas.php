<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Artist',
    required: ['id', 'name', 'image'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'name', type: 'string', example: 'Daft Punk'),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'artist/cover.webp'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ArtistResourceResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/Artist'),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ArtistsSearchPaginatedData',
    required: ['items', 'pagination'],
    properties: [
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Artist'),
        ),
        new OA\Property(property: 'pagination', ref: '#/components/schemas/Pagination'),
    ],
    type: 'object',
)]
final class ArtistSchemas
{
}

<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Artist',
    required: ['id', 'name', 'image'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Daft Punk'),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'artist/cover.webp'),
    ],
    type: 'object',
)]
final class ArtistSchemas
{
}


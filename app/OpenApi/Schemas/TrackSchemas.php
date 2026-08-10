<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Track',
    required: ['id', 'name', 'cover', 'song', 'artists', 'time'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'name', type: 'string', example: 'Harder Better Faster Stronger'),
        new OA\Property(property: 'cover', type: 'string', nullable: true, example: '/image/track/cover.webp'),
        new OA\Property(property: 'song', type: 'string', example: 'tracks/song.ogg'),
        new OA\Property(
            property: 'artists',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Artist'),
        ),
        new OA\Property(property: 'time', type: 'integer', example: 224),
    ],
    type: 'object',
)]
final class TrackSchemas
{
}


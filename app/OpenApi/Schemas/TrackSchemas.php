<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Track',
    required: ['id', 'name', 'cover', 'song', 'artists', 'time', 'loudness'],
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
        new OA\Property(property: 'loudness', ref: '#/components/schemas/TrackLoudness'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TrackLoudness',
    description: 'Measured source audio loudness metadata. Decimal database values are returned as strings; each measurement is null when not yet available.',
    required: ['integratedLufs', 'truePeakDb'],
    properties: [
        new OA\Property(
            property: 'integratedLufs',
            description: 'Integrated loudness of the source track in LUFS.',
            type: 'string',
            nullable: true,
            example: '-14.20',
        ),
        new OA\Property(
            property: 'truePeakDb',
            description: 'True peak level of the source track in dBTP.',
            type: 'string',
            nullable: true,
            example: '-1.50',
        ),
    ],
    type: 'object',
)]
final class TrackSchemas
{
}


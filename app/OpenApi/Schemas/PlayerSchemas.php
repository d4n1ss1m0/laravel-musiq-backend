<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PlayerTracksData',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/Track'),
)]
#[OA\Schema(
    schema: 'PlayerTrackResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/Track'),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
final class PlayerSchemas
{
}

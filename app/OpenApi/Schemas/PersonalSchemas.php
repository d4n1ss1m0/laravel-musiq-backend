<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PersonalAddedTracksData',
    required: ['items', 'pagination'],
    properties: [
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Track'),
        ),
        new OA\Property(property: 'pagination', ref: '#/components/schemas/Pagination'),
    ],
    type: 'object',
)]
final class PersonalSchemas
{
}

<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HistoryStoreRequest',
    required: ['id'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'source', type: 'string', nullable: true, enum: ['playlist', 'artist'], example: 'playlist'),
        new OA\Property(property: 'sourceId', type: 'string', format: 'uuid', nullable: true, example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
    ],
    type: 'object',
)]
final class HistorySchemas
{
}

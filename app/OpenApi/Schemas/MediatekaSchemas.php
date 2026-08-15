<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MediatekaItemType',
    type: 'string',
    enum: ['playlist', 'artist'],
    example: 'playlist',
)]
#[OA\Schema(
    schema: 'MediatekaItemPayload',
    oneOf: [
        new OA\Schema(ref: '#/components/schemas/Playlist'),
        new OA\Schema(ref: '#/components/schemas/Artist'),
    ],
)]
#[OA\Schema(
    schema: 'MediatekaItem',
    required: ['type', 'item'],
    properties: [
        new OA\Property(property: 'type', type: 'string', enum: ['Playlist', 'Artist'], example: 'Playlist'),
        new OA\Property(property: 'item', ref: '#/components/schemas/MediatekaItemPayload'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'MediatekaCollectionResponse',
    required: ['data'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/MediatekaItem'),
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'MediatekaMediaRequest',
    required: ['id'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'MediatekaActionResponse',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
        new OA\Schema(
            properties: [
                new OA\Property(property: 'data', type: 'string', example: 'success'),
            ],
            type: 'object',
        ),
    ],
)]
final class MediatekaSchemas
{
}

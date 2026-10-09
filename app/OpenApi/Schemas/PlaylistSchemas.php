<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Playlist',
    required: ['id', 'name', 'image', 'type', 'isOwner'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'name', type: 'string', example: 'My playlist'),
        new OA\Property(
            property: 'image',
            type: 'array',
            items: new OA\Items(type: 'string', example: '/image/playlist/cover.webp'),
            example: ['/image/playlist/cover.webp'],
        ),
        new OA\Property(
            property: 'type',
            required: ['id', 'name'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', enum: ['public', 'private', 'favourite'], example: 'public'),
            ],
            type: 'object',
        ),
        new OA\Property(
            property: 'isOwner',
            description: 'Whether the authenticated user owns this playlist.',
            type: 'boolean',
            example: true,
        ),
        new OA\Property(
            property: 'tracks',
            description: 'Playlist track statistics. Included when requesting or creating a playlist; omitted in playlist lists.',
            required: ['count', 'duration'],
            properties: [
                new OA\Property(
                    property: 'count',
                    description: 'Number of tracks, including zero for an empty playlist.',
                    type: 'integer',
                    minimum: 0,
                    example: 12,
                ),
                new OA\Property(
                    property: 'duration',
                    description: 'Total duration in seconds, including zero for an empty playlist.',
                    type: 'integer',
                    minimum: 0,
                    example: 2688,
                ),
            ],
            type: 'object',
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistWithTrackStats',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/Playlist'),
        new OA\Schema(type: 'object', required: ['tracks']),
    ],
)]
#[OA\Schema(
    schema: 'PlaylistResourceResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistWithTrackStats'),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistCreateRequest',
    required: ['name', 'type'],
    properties: [
        new OA\Property(property: 'file', type: 'string', format: 'binary', nullable: true),
        new OA\Property(property: 'name', type: 'string', example: 'My playlist'),
        new OA\Property(property: 'type', type: 'string', enum: ['public', 'private'], example: 'public'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistUpdateRequest',
    properties: [
        new OA\Property(property: 'file', type: 'string', format: 'binary', nullable: true),
        new OA\Property(property: 'name', type: 'string', example: 'Updated playlist'),
        new OA\Property(property: 'type', type: 'string', enum: ['public', 'private'], example: 'private'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistAddTracksRequest',
    required: ['ids'],
    properties: [
        new OA\Property(
            property: 'ids',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uuid'),
            minItems: 1,
            uniqueItems: true,
            example: ['018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'],
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistRemoveTracksRequest',
    required: ['ids'],
    properties: [
        new OA\Property(
            property: 'ids',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uuid'),
            minItems: 1,
            example: ['018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'],
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistOrderRequest',
    required: ['id', 'order'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'order', type: 'integer', minimum: 1, example: 2),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistImportRequest',
    required: ['id'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistActionMessage',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Playlist updated'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistAddTracksResult',
    required: ['message', 'playlistId', 'tracksIds'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Track added'),
        new OA\Property(property: 'playlistId', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(
            property: 'tracksIds',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uuid'),
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistQueueItem',
    required: ['playlist_item_id', 'track_id', 'position'],
    properties: [
        new OA\Property(property: 'playlist_item_id', type: 'integer', example: 12),
        new OA\Property(property: 'track_id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'position', type: 'integer', example: 1),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaylistTracksPaginatedData',
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
final class PlaylistSchemas
{
}

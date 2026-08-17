<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MainPageRecentlyPlayedTracksData',
    required: ['trackIds', 'tracks'],
    properties: [
        new OA\Property(
            property: 'trackIds',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uuid'),
            example: ['018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'],
        ),
        new OA\Property(
            property: 'tracks',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Track'),
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'MainPageRecentlyPlayedPlaylistsData',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/Playlist'),
)]
#[OA\Schema(
    schema: 'MainPageMostPlayablePlaylistsData',
    description: 'The first two items are always the favourite playlist and the history item, followed by most playable playlists.',
    type: 'array',
    minItems: 2,
    items: new OA\Items(
        required: ['id', 'name', 'image', 'type'],
        properties: [
            new OA\Property(property: 'id', type: 'string', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
            new OA\Property(property: 'name', type: 'string', example: 'My playlist'),
            new OA\Property(
                property: 'image',
                type: 'array',
                items: new OA\Items(type: 'string'),
                example: ['/image/playlist/cover.webp'],
            ),
            new OA\Property(
                property: 'type',
                required: ['id', 'name'],
                properties: [
                    new OA\Property(
                        property: 'id',
                        description: 'Numeric playlist type ID for playlist items. The service history item uses "history".',
                        oneOf: [
                            new OA\Schema(type: 'integer', example: 2),
                            new OA\Schema(type: 'string', example: 'history'),
                        ],
                    ),
                    new OA\Property(property: 'name', type: 'string', example: 'public'),
                ],
                type: 'object',
            ),
        ],
        type: 'object',
    ),
    example: [
        [
            'id' => '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd',
            'name' => 'Favourite',
            'image' => [],
            'type' => [
                'id' => 1,
                'name' => 'favourite',
            ],
        ],
        [
            'id' => 'history',
            'name' => 'history',
            'image' => [],
            'type' => [
                'id' => 'history',
                'name' => 'history',
            ],
        ],
        [
            'id' => '018ff4e6-5d84-7000-8e14-2f6d17c1b9fe',
            'name' => 'Most played playlist',
            'image' => ['/image/playlist/cover.webp'],
            'type' => [
                'id' => 2,
                'name' => 'public',
            ],
        ],
    ],
)]
#[OA\Schema(
    schema: 'MainPageRecentlyAddedTracksData',
    required: ['itemsIds', 'items', 'pagination'],
    properties: [
        new OA\Property(
            property: 'itemsIds',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uuid'),
            example: ['018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'],
        ),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Track'),
        ),
        new OA\Property(property: 'pagination', ref: '#/components/schemas/Pagination'),
    ],
    type: 'object',
)]
final class MainPageSchemas
{
}

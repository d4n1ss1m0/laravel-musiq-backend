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

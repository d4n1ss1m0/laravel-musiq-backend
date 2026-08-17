<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class MainPagePaths
{
    #[OA\Get(
        path: '/main-page/recently-played-tracks',
        summary: 'Get recently played tracks',
        security: [['tokenAuth' => []]],
        tags: ['Main Page'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Recently played tracks',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/MainPageRecentlyPlayedTracksData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function getRecentlyPlayedTracks(): void
    {
    }

    #[OA\Get(
        path: '/main-page/recently-played-playlists',
        summary: 'Get recently played playlists',
        security: [['tokenAuth' => []]],
        tags: ['Main Page'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Recently played playlists',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/MainPageRecentlyPlayedPlaylistsData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function getRecentlyPlayedPlaylists(): void
    {
    }

    #[OA\Get(
        path: '/main-page/most-playable-playlists',
        summary: 'Get most playable playlists',
        security: [['tokenAuth' => []]],
        tags: ['Main Page'],
        parameters: [
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 10),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Most playable playlists',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/MainPageMostPlayablePlaylistsData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function getMostPlayablePlaylists(): void
    {
    }

    #[OA\Get(
        path: '/main-page/recently-added-tracks',
        summary: 'Get recently added tracks',
        security: [['tokenAuth' => []]],
        tags: ['Main Page'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1),
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Recently added tracks',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/MainPageRecentlyAddedTracksData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function getRecentlyAddedTracks(): void
    {
    }
}

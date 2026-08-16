<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class PlaylistPaths
{
    #[OA\Get(
        path: '/playlist/{uuid}',
        summary: 'Get playlist',
        security: [['tokenAuth' => []]],
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaylistResourceResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
        ],
    )]
    public function getPlaylist(): void
    {
    }

    #[OA\Get(
        path: '/playlist/{uuid}/tracks',
        summary: 'Get playlist tracks',
        security: [['tokenAuth' => []]],
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
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
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50),
            ),
            new OA\Parameter(
                name: 'query',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', maxLength: 255),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist tracks',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistTracksPaginatedData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function getTracks(): void
    {
    }

    #[OA\Get(
        path: '/playlist/{uuid}/queue',
        summary: 'Get playlist queue',
        security: [['tokenAuth' => []]],
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist queue',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(ref: '#/components/schemas/PlaylistQueueItem'),
                                ),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
        ],
    )]
    public function getQueue(): void
    {
    }

    #[OA\Post(
        path: '/playlist/favourite/add',
        summary: 'Add tracks to favourite playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaylistAddTracksRequest'),
        ),
        tags: ['Playlist'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tracks added to favourite playlist',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
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
        ],
    )]
    public function addFavourite(): void
    {
    }

    #[OA\Delete(
        path: '/playlist/favourite/remove',
        summary: 'Remove tracks from favourite playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaylistRemoveTracksRequest'),
        ),
        tags: ['Playlist'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tracks removed from favourite playlist',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
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
        ],
    )]
    public function removeFavourite(): void
    {
    }

    #[OA\Post(
        path: '/playlist/create',
        summary: 'Create playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/PlaylistCreateRequest'),
            ),
        ),
        tags: ['Playlist'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist created',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/Playlist'),
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
        ],
    )]
    public function create(): void
    {
    }

    #[OA\Patch(
        path: '/playlist/{uuid}',
        summary: 'Update playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/PlaylistUpdateRequest'),
            ),
        ),
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist updated',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/playlist/{uuid}',
        summary: 'Delete playlist',
        security: [['tokenAuth' => []]],
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist deleted',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
        ],
    )]
    public function delete(): void
    {
    }

    #[OA\Post(
        path: '/playlist/{uuid}/add',
        summary: 'Add tracks to playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaylistAddTracksRequest'),
        ),
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tracks added',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistAddTracksResult'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function addTrack(): void
    {
    }

    #[OA\Delete(
        path: '/playlist/{uuid}/remove',
        summary: 'Remove tracks from playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaylistRemoveTracksRequest'),
        ),
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tracks removed',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function removeTrack(): void
    {
    }

    #[OA\Put(
        path: '/playlist/{uuid}/order',
        summary: 'Change track order in playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaylistOrderRequest'),
        ),
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order changed',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function order(): void
    {
    }

    #[OA\Post(
        path: '/playlist/{uuid}/import-from-playlist',
        summary: 'Import tracks from another playlist',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaylistImportRequest'),
        ),
        tags: ['Playlist'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Target playlist UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playlist imported',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaylistActionMessage'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playlist not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function importFromPlaylist(): void
    {
    }
}

<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class MediatekaPaths
{
    #[OA\Get(
        path: '/mediateka',
        summary: 'Get mediateka',
        security: [['tokenAuth' => []]],
        tags: ['Mediateka'],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['recent', 'alphabet', 'created_at'], example: 'created_at'),
            ),
            new OA\Parameter(
                name: 'query',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Daft Punk'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Mediateka items',
                content: new OA\JsonContent(ref: '#/components/schemas/MediatekaCollectionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function getMediateka(): void
    {
    }

    #[OA\Post(
        path: '/mediateka/{type}/add',
        summary: 'Add media to mediateka',
        description: 'For playlist items the playlist must not belong to the current user.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MediatekaMediaRequest'),
        ),
        tags: ['Mediateka'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Media type',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/MediatekaItemType'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Media added',
                content: new OA\JsonContent(ref: '#/components/schemas/MediatekaActionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Media not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function addMedia(): void
    {
    }

    #[OA\Delete(
        path: '/mediateka/{type}/remove',
        summary: 'Remove media from mediateka',
        description: 'For playlist items the playlist must not belong to the current user.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MediatekaMediaRequest'),
        ),
        tags: ['Mediateka'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Media type',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/MediatekaItemType'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Media removed',
                content: new OA\JsonContent(ref: '#/components/schemas/MediatekaActionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Media not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function removeMedia(): void
    {
    }

    #[OA\Patch(
        path: '/mediateka/{type}/pin',
        summary: 'Pin mediateka item',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MediatekaMediaRequest'),
        ),
        tags: ['Mediateka'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Media type',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/MediatekaItemType'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Media pinned',
                content: new OA\JsonContent(ref: '#/components/schemas/MediatekaActionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Media not found in mediateka'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function pinItem(): void
    {
    }

    #[OA\Patch(
        path: '/mediateka/{type}/unpin',
        summary: 'Unpin mediateka item',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/MediatekaMediaRequest'),
        ),
        tags: ['Mediateka'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Media type',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/MediatekaItemType'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Media unpinned',
                content: new OA\JsonContent(ref: '#/components/schemas/MediatekaActionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Media not found in mediateka'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function unpinItem(): void
    {
    }
}

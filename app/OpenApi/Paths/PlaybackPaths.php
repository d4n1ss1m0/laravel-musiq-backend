<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class PlaybackPaths
{
    #[OA\Post(
        path: '/playback/snapshot',
        summary: 'Create playback snapshot',
        description: 'Creates a new playback session for the current user. Existing session is replaced and the saved pause position is null.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSnapshotRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback snapshot created',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSnapshotResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function snapshot(): void
    {
    }

    #[OA\Patch(
        path: '/playback/shuffle',
        summary: 'Toggle playback shuffle',
        description: 'Enables or disables shuffle for the current playback session.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackShuffleRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback session',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSessionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function shuffle(): void
    {
    }

    #[OA\Post(
        path: '/playback/next',
        summary: 'Switch to next track',
        description: 'Switches to the next track and clears the saved pause position. At the end of the queue it can finish playback or restart the queue.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackRequeueRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback session',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSessionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function next(): void
    {
    }

    #[OA\Post(
        path: '/playback/prev',
        summary: 'Switch to previous track',
        description: 'Switches to the previous track and clears the saved pause position. From the first track it switches to the last track.',
        security: [['tokenAuth' => []]],
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback session',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSessionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function prev(): void
    {
    }

    #[OA\Patch(
        path: '/playback/repeat',
        summary: 'Change repeat mode',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackRepeatRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback session',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSessionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function repeat(): void
    {
    }

    #[OA\Post(
        path: '/playback/play',
        summary: 'Play current or selected track',
        description: 'Sets playback state to playing and clears the saved pause position. If placement is provided, selects that position in the current queue. To resume after an app restart, first read pausePositionSecond from GET /playback and seek the player before calling play.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackPlayRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback session',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSessionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 404,
                description: 'Playback session or selected queue position not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid placement',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string'),
                            ),
                        ),
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'code', type: 'integer', example: 422),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function play(): void
    {
    }

    #[OA\Post(
        path: '/playback/pause',
        summary: 'Set playback state to paused',
        description: 'Pauses the current track and saves its position in seconds without changing its position in the queue.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackPauseRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback session',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackSessionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 404,
                description: 'Playback session not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Missing, non-integer, or negative seconds',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string'),
                            ),
                        ),
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'code', type: 'integer', example: 422),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function pause(): void
    {
    }

    #[OA\Post(
        path: '/playback/add',
        summary: 'Add track to playback queue',
        description: 'Adds a track immediately after the current queue item (next) or at the end of the queue (tail). Read GET /playback to get the updated queue.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackAddToQueueRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Track added to queue',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackQueueActionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Invalid request fields',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
            new OA\Response(
                response: 500,
                description: 'Queue operation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function add(): void
    {
    }

    #[OA\Post(
        path: '/playback/move',
        summary: 'Move track within playback queue',
        description: 'Moves the queue item at currentPosition to position. Both positions are one-based and refer to playbackPosition from GET /playback. The item currently playing remains the same; its currentPosition in the session is updated. When shuffle is disabled, the new order is also saved as the source order. The response contains only success; read GET /playback for the updated queue. Send moves sequentially because a later request uses positions from the latest queue order.',
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PlaybackMoveRequest'),
        ),
        tags: ['Playback'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Queue item moved',
                content: new OA\JsonContent(ref: '#/components/schemas/PlaybackQueueActionResponse'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(
                response: 422,
                description: 'Missing or non-integer request fields',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
            new OA\Response(
                response: 500,
                description: 'Session or queue item not found, position outside queue, or queue operation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function move(): void
    {
    }

    #[OA\Get(
        path: '/playback',
        summary: 'Get playback queue',
        description: 'Returns the current playback session, including the saved pause position, and a paginated queue for the current user. Queue items expose playbackPosition for play and move requests.',
        security: [['tokenAuth' => []]],
        tags: ['Playback'],
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
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Playback queue',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/ApiSuccessResponse'),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'data', ref: '#/components/schemas/PlaybackQueuePaginatedData'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Playback session not found'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function queue(): void
    {
    }
}

<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class PlaybackPaths
{
    #[OA\Post(
        path: '/playback/snapshot',
        summary: 'Create playback snapshot',
        description: 'Creates a new playback session for the current user. Existing session is replaced.',
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
        description: 'Switches to the next track. At the end of the queue it can finish playback or restart the queue.',
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
        description: 'Switches to the previous track. From the first track it switches to the last track.',
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
        summary: 'Set playback state to playing',
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
    public function play(): void
    {
    }

    #[OA\Post(
        path: '/playback/pause',
        summary: 'Set playback state to paused',
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
    public function pause(): void
    {
    }
}

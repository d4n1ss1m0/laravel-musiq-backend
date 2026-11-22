<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class HistoryPaths
{
    #[OA\Post(
        path: '/history',
        summary: 'Add track to listening history (legacy)',
        description: 'Legacy play counter with a cooldown. Does not record listened seconds. New clients should use POST /history/listen.',
        deprecated: true,
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/HistoryStoreRequest'),
        ),
        tags: ['History'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'History item recorded or ignored by the cooldown',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryStoreResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/PlainErrorResponse'),
            ),
            new OA\Response(
                response: 404,
                description: 'Track or source not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryValidationErrorResponse'),
            ),
        ],
    )]
    public function store(): void {}

    #[OA\Post(
        path: '/history/listen',
        summary: 'Synchronize cumulative listening progress',
        description: "Frontend setup:\n"
            . "1. Create a new UUID attemptId and fixed startedAt when a track starts; send the first report then. Reuse both for retries and later reports of that play.\n"
            . "2. Count seconds while audio actually plays. Pause counting during pause and seeking; do not use the playback position.\n"
            . "3. Send cumulative listenedSeconds about every 10–15 seconds, and on pause or app background when possible. Retry with the same attemptId and latest cumulative value after network loss.\n"
            . "4. On skip, next, previous, or track end send a final report with finished=true. Start the next play with a new attemptId. A later report with more seconds for a finished attempt is accepted.\n"
            . "The server credits only the increase for each attempt. listenedSeconds may exceed the track duration when a section is replayed. These seconds are for listening totals, not resume position.",
        security: [['tokenAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/HistoryListenRequest'),
        ),
        tags: ['History'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listening progress synchronized',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryListenResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/PlainErrorResponse'),
            ),
            new OA\Response(
                response: 404,
                description: 'Track or source not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
            new OA\Response(
                response: 409,
                description: 'Attempt ID already belongs to another user, track, or source',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryValidationErrorResponse'),
            ),
        ],
    )]
    public function listen(): void {}

    #[OA\Get(
        path: '/history',
        summary: 'Get listening history',
        description: 'Returns history groups ordered by most recent activity and grouped by date and consecutive source. Pagination counts groups, not tracks. An empty page returns data: []. This endpoint does not return listened seconds.',
        security: [['tokenAuth' => []]],
        tags: ['History'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50, default: 10)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listening history grouped by date',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryGetResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/PlainErrorResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid page or limit',
                content: new OA\JsonContent(ref: '#/components/schemas/HistoryValidationErrorResponse'),
            ),
            new OA\Response(
                response: 500,
                description: 'History query failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiErrorResponse'),
            ),
        ],
    )]
    public function get(): void {}
}

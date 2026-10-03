<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HistoryStoreRequest',
    required: ['id'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'source', type: 'string', nullable: true, enum: ['playlist', 'artist']),
        new OA\Property(property: 'sourceId', type: 'string', format: 'uuid', nullable: true),
    ],
    type: 'object',
)]
final class HistorySchemas {}

#[OA\Schema(
    schema: 'HistoryListenRequest',
    required: ['attemptId', 'id', 'startedAt', 'listenedSeconds'],
    properties: [
        new OA\Property(property: 'attemptId', type: 'string', format: 'uuid'),
        new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Track UUID'),
        new OA\Property(property: 'source', type: 'string', nullable: true, enum: ['playlist', 'artist']),
        new OA\Property(property: 'sourceId', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'startedAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'listenedSeconds', type: 'integer', minimum: 0),
        new OA\Property(property: 'finished', type: 'boolean', default: false),
    ],
    type: 'object',
)]
final class HistoryListenSchema {}

#[OA\Schema(
    schema: 'HistoryListenResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(
            property: 'data',
            required: ['attemptId', 'creditedSeconds', 'attemptListenedSeconds', 'listenedSeconds', 'playCount', 'finished'],
            properties: [
                new OA\Property(property: 'attemptId', type: 'string', format: 'uuid'),
                new OA\Property(property: 'creditedSeconds', description: 'Seconds credited by this request', type: 'integer', minimum: 0),
                new OA\Property(property: 'attemptListenedSeconds', description: 'Total seconds credited for this attempt', type: 'integer', minimum: 0),
                new OA\Property(property: 'listenedSeconds', description: 'Aggregated seconds in the history item', type: 'integer', minimum: 0),
                new OA\Property(property: 'playCount', type: 'integer', minimum: 1),
                new OA\Property(property: 'finished', type: 'boolean'),
            ],
            type: 'object',
        ),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
final class HistoryListenResponseSchema {}

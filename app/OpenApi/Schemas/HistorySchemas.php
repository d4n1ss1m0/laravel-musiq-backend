<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HistoryStoreRequest',
    required: ['id'],
    properties: [
        new OA\Property(property: 'id', description: 'Track UUID', type: 'string', format: 'uuid'),
        new OA\Property(property: 'source', description: 'Provide together with sourceId.', type: 'string', nullable: true, enum: ['playlist', 'artist']),
        new OA\Property(property: 'sourceId', description: 'Playlist or artist UUID; provide together with source.', type: 'string', format: 'uuid', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'HistoryStoreResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', type: 'string', example: 'success'),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
final class HistorySchemas {}

#[OA\Schema(
    schema: 'HistoryListenRequest',
    required: ['attemptId', 'id', 'startedAt', 'listenedSeconds'],
    properties: [
        new OA\Property(property: 'attemptId', description: 'New UUID for each track start. Reuse it for all reports and retries of the same attempt.', type: 'string', format: 'uuid'),
        new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Track UUID'),
        new OA\Property(property: 'source', description: 'Provide together with sourceId; keep unchanged for this attempt.', type: 'string', nullable: true, enum: ['playlist', 'artist']),
        new OA\Property(property: 'sourceId', description: 'Playlist or artist UUID; provide together with source.', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'startedAt', description: 'Track start time. Keep unchanged for retries; cannot be in the future.', type: 'string', format: 'date-time', example: '2026-09-25T10:00:00Z'),
        new OA\Property(property: 'listenedSeconds', description: 'Cumulative seconds of audio actually played in this attempt, not playback position or a delta. Must not exceed track duration.', type: 'integer', minimum: 0, example: 47),
        new OA\Property(property: 'finished', description: 'Set true on the final report after skip, track change, or track end. Later larger reports are still accepted.', type: 'boolean', default: false),
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

#[OA\Schema(
    schema: 'HistoryValidationErrorResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(
            property: 'data',
            description: 'Field errors for request validation, or a message for a business rule error.',
            oneOf: [
                new OA\Schema(
                    type: 'object',
                    additionalProperties: new OA\AdditionalProperties(
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                    ),
                ),
                new OA\Schema(type: 'string'),
            ],
        ),
        new OA\Property(property: 'status', type: 'string', example: 'error'),
        new OA\Property(property: 'code', type: 'integer', example: 422),
    ],
    type: 'object',
)]
final class HistoryValidationErrorSchema {}

#[OA\Schema(
    schema: 'HistoryGroupItem',
    required: ['type', 'item', 'tracksCount', 'trackIds'],
    properties: [
        new OA\Property(property: 'type', type: 'string', enum: ['playlist', 'artist', 'tracks']),
        new OA\Property(property: 'item', description: 'Playlist or artist resource, or null for a tracks group or deleted source.', type: 'object', nullable: true),
        new OA\Property(property: 'tracksCount', description: 'Number of track IDs in this group.', type: 'integer', minimum: 0),
        new OA\Property(property: 'trackIds', type: 'array', items: new OA\Items(type: 'string', format: 'uuid')),
        new OA\Property(property: 'cover', description: 'Present only for a tracks group.', type: 'string', nullable: true),
    ],
    type: 'object',
)]
final class HistoryGroupItemSchema {}

#[OA\Schema(
    schema: 'HistoryDateGroup',
    required: ['date', 'items'],
    properties: [
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2026-09-25'),
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/HistoryGroupItem')),
    ],
    type: 'object',
)]
final class HistoryDateGroupSchema {}

#[OA\Schema(
    schema: 'HistoryGetResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/HistoryDateGroup')),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
final class HistoryGetResponseSchema {}

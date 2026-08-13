<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PlaybackSnapshotRequest',
    required: ['source', 'sourceId', 'id', 'repeatType', 'shuffle'],
    properties: [
        new OA\Property(property: 'source', type: 'string', enum: ['playlist', 'artist', 'track'], example: 'playlist'),
        new OA\Property(property: 'sourceId', type: 'string', format: 'uuid', example: '019fc713-f522-73fc-8f2d-b2938bdaa5a0'),
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '6654fcfe-cb4f-4c8f-951c-57eb5bde50a1'),
        new OA\Property(property: 'repeatType', type: 'string', enum: ['off', 'track', 'queue'], example: 'off'),
        new OA\Property(property: 'shuffle', type: 'boolean', example: false),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackShuffleRequest',
    required: ['shuffle'],
    properties: [
        new OA\Property(property: 'shuffle', type: 'boolean', example: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackRequeueRequest',
    properties: [
        new OA\Property(property: 'requeue', type: 'boolean', example: false),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackRepeatRequest',
    required: ['repeatType'],
    properties: [
        new OA\Property(property: 'repeatType', type: 'string', enum: ['off', 'track', 'queue'], example: 'queue'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackSourceInfo',
    required: ['sourceType', 'sourceId'],
    properties: [
        new OA\Property(property: 'sourceType', type: 'string', enum: ['playlist', 'artist', 'track'], example: 'playlist'),
        new OA\Property(property: 'sourceId', type: 'string', format: 'uuid', example: '019fc713-f522-73fc-8f2d-b2938bdaa5a0'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackSession',
    required: ['source', 'currentTrackId', 'currentPosition', 'prev', 'next', 'shuffle', 'repeatMode', 'state'],
    properties: [
        new OA\Property(property: 'source', ref: '#/components/schemas/PlaybackSourceInfo'),
        new OA\Property(property: 'currentTrackId', type: 'string', format: 'uuid', example: '6654fcfe-cb4f-4c8f-951c-57eb5bde50a1'),
        new OA\Property(property: 'currentPosition', type: 'integer', minimum: 1, example: 1),
        new OA\Property(property: 'prev', type: 'string', format: 'uuid', nullable: true, example: null),
        new OA\Property(property: 'next', type: 'string', format: 'uuid', nullable: true, example: '6654fcfe-cb4f-4c8f-951c-57eb5bde50a2'),
        new OA\Property(property: 'shuffle', type: 'boolean', example: false),
        new OA\Property(property: 'repeatMode', type: 'string', enum: ['off', 'track', 'queue'], example: 'off'),
        new OA\Property(property: 'state', type: 'string', enum: ['playing', 'paused', 'finished'], example: 'playing'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackSessionResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/PlaybackSession'),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlaybackSnapshotResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', type: 'string', example: 'success'),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
final class PlaybackSchemas
{
}

<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddTrackArtistById',
    required: ['id'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
        new OA\Property(property: 'name', type: 'string', nullable: true, example: 'Daft Punk'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'AddTrackArtistByName',
    required: ['name'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', nullable: true, example: null),
        new OA\Property(property: 'name', type: 'string', example: 'Daft Punk'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'AddTrackArtistInput',
    oneOf: [
        new OA\Schema(ref: '#/components/schemas/AddTrackArtistById'),
        new OA\Schema(ref: '#/components/schemas/AddTrackArtistByName'),
    ],
)]
#[OA\Schema(
    schema: 'AddTrackByFileRequest',
    required: ['name', 'artists', 'file', 'cover'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Harder Better Faster Stronger'),
        new OA\Property(
            property: 'artists',
            description: 'Each artist must contain either an existing artist UUID in id or a name for a new artist.',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/AddTrackArtistInput'),
        ),
        new OA\Property(property: 'file', type: 'string', format: 'binary', description: 'Audio file: mp3, flac, wav'),
        new OA\Property(property: 'cover', type: 'string', format: 'binary', description: 'Cover image: jpg, png, webp'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ParseTrackLinkRequest',
    required: ['link', 'service'],
    properties: [
        new OA\Property(property: 'link', type: 'string', example: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
        new OA\Property(property: 'service', type: 'string', enum: ['youtube', 'govno'], example: 'youtube'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ParseTrackLinkResult',
    required: ['audio', 'cover', 'name'],
    properties: [
        new OA\Property(property: 'audio', type: 'string', nullable: true, example: 'tmp/018ff4e6-5d84-7000-8e14-2f6d17c1b9fd.mp3'),
        new OA\Property(property: 'cover', type: 'string', nullable: true, example: 'tmp/018ff4e6-5d84-7000-8e14-2f6d17c1b9fd.webp'),
        new OA\Property(property: 'name', type: 'string', nullable: true, example: 'Harder Better Faster Stronger'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'AddTrackAfterParseRequest',
    required: ['file', 'name', 'artists'],
    properties: [
        new OA\Property(property: 'file', type: 'string', example: 'tmp/018ff4e6-5d84-7000-8e14-2f6d17c1b9fd.mp3'),
        new OA\Property(property: 'name', type: 'string', example: 'Harder Better Faster Stronger'),
        new OA\Property(
            property: 'artists',
            description: 'Each artist must contain either an existing artist UUID in id or a name for a new artist.',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/AddTrackArtistInput'),
        ),
        new OA\Property(property: 'cover', type: 'string', format: 'binary', nullable: true, description: 'Cover image: jpg, png, webp'),
        new OA\Property(property: 'coverName', type: 'string', nullable: true, example: 'tmp/018ff4e6-5d84-7000-8e14-2f6d17c1b9fd.webp'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'AddTrackResult',
    required: ['trackId'],
    properties: [
        new OA\Property(property: 'trackId', type: 'string', format: 'uuid', example: '018ff4e6-5d84-7000-8e14-2f6d17c1b9fd'),
    ],
    type: 'object',
)]
final class AddTrackSchemas
{
}

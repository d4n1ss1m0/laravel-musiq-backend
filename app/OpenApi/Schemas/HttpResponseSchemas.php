<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiSuccessResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', nullable: true),
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ApiErrorResponse',
    required: ['data', 'status', 'code'],
    properties: [
        new OA\Property(property: 'data', nullable: true, example: 'Validation error'),
        new OA\Property(property: 'status', type: 'string', example: 'error'),
        new OA\Property(property: 'code', type: 'integer', example: 500),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string'),
            ),
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PlainErrorResponse',
    required: ['error'],
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'Pagination',
    required: ['total', 'perPage', 'currentPage', 'lastPage'],
    properties: [
        new OA\Property(property: 'total', type: 'integer', example: 42),
        new OA\Property(property: 'perPage', type: 'integer', example: 10),
        new OA\Property(property: 'currentPage', type: 'integer', example: 1),
        new OA\Property(property: 'lastPage', type: 'integer', example: 5),
    ],
    type: 'object',
)]
final class HttpResponseSchemas
{
}

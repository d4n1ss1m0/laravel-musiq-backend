<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class AuthPaths
{
    #[OA\Post(
        path: '/auth/login',
        summary: 'Login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['login', 'password'],
                properties: [
                    new OA\Property(property: 'login', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ],
            ),
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful authentication',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'accessToken', type: 'string'),
                        new OA\Property(property: 'refreshToken', type: 'string'),
                    ],
                ),
            ),
        ],
    )]
    public function login(): void
    {
    }

    #[OA\Post(
        path: '/auth/refresh',
        summary: 'Refresh tokens',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['refresh_token'],
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string'),
                ],
            ),
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tokens refreshed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'accessToken', type: 'string'),
                        new OA\Property(property: 'refreshToken', type: 'string'),
                    ],
                ),
            ),
        ],
    )]
    public function refresh(): void
    {
    }
}

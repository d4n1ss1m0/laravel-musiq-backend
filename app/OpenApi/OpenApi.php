<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Musiq API',
)]
#[OA\Server(
    url: 'https://musiq.su/api',
    description: 'Production API',
)]
#[OA\SecurityScheme(
    securityScheme: 'tokenAuth',
    type: 'apiKey',
    in: 'header',
    name: 'token',
)]
class OpenApi
{
}

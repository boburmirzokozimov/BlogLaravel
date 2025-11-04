<?php

declare(strict_types=1);

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Token',
    title: 'JWT Token',
    description: 'JWT authentication token response',
    properties: [
        new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
        new OA\Property(property: 'expires_in', type: 'integer', example: 3600, description: 'Token expiration time in seconds'),
    ],
    type: 'object'
)]
class TokenSchema
{
}

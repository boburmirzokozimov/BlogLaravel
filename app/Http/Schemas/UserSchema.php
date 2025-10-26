<?php

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    title: 'User',
    description: 'User model with UUID identifier',
    required: ['id', 'name', 'email'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'string',
            format: 'uuid',
            description: 'User unique identifier (UUID v4)',
            example: '550e8400-e29b-41d4-a716-446655440000'
        ),
        new OA\Property(
            property: 'name',
            type: 'string',
            description: 'User full name',
            example: 'John Doe'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            description: 'User email address',
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'email_verified_at',
            type: 'string',
            format: 'date-time',
            description: 'Email verification timestamp',
            example: '2024-01-15T10:30:00.000000Z',
            nullable: true
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            description: 'User creation timestamp',
            example: '2024-01-15T10:30:00.000000Z'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            description: 'User last update timestamp',
            example: '2024-01-15T10:30:00.000000Z'
        ),
    ]
)]
class UserSchema
{
    // This class is only used for OpenAPI documentation
}

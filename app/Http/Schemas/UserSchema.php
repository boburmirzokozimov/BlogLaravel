<?php

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    title: 'User',
    description: 'User model with UUID identifier',
    required: ['id', 'name', 'email', 'status'],
    properties: [
        new OA\Property(
            property: 'id',
            description: 'User unique identifier (UUID v4)',
            type: 'string',
            format: 'uuid',
            example: '550e8400-e29b-41d4-a716-446655440000'
        ),
        new OA\Property(
            property: 'name',
            description: 'User full name',
            type: 'string',
            example: 'John Doe'
        ),
        new OA\Property(
            property: 'email',
            description: 'User email address',
            type: 'string',
            format: 'email',
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'status',
            description: 'User status',
            type: 'string',
            enum: ['active', 'inactive', 'pending', 'suspended'],
            example: 'active'
        ),
        new OA\Property(
            property: 'email_verified_at',
            description: 'Email verification timestamp',
            type: 'string',
            format: 'date-time',
            example: '2024-01-15T10:30:00.000000Z',
            nullable: true
        ),
        new OA\Property(
            property: 'created_at',
            description: 'User creation timestamp',
            type: 'string',
            format: 'date-time',
            example: '2024-01-15T10:30:00.000000Z'
        ),
        new OA\Property(
            property: 'updated_at',
            description: 'User last update timestamp',
            type: 'string',
            format: 'date-time',
            example: '2024-01-15T10:30:00.000000Z'
        ),
    ]
)]
class UserSchema
{
    // This class is only used for OpenAPI documentation
}

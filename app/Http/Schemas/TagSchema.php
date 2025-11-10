<?php

declare(strict_types=1);

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Tag',
    title: 'Tag',
    description: 'Tag model',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: 'a1b2c3d4-e5f6-7890-1234-567890abcdef'),
        new OA\Property(property: 'name', type: 'string', example: 'PHP'),
        new OA\Property(property: 'slug', type: 'string', example: 'php'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true, example: '2023-10-27T10:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true, example: '2023-10-27T10:00:00.000000Z'),
    ],
    type: 'object'
)]
class TagSchema
{
}


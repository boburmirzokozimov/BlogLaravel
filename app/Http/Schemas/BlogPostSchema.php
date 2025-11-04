<?php

declare(strict_types=1);

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BlogPost',
    title: 'Blog Post',
    description: 'Blog post model',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: 'a1b2c3d4-e5f6-7890-1234-567890abcdef'),
        new OA\Property(property: 'title', type: 'string', example: 'My First Blog Post'),
        new OA\Property(property: 'slug', type: 'string', example: 'my-first-blog-post'),
        new OA\Property(property: 'content', type: 'string', example: 'This is the full content of my blog post...'),
        new OA\Property(property: 'author_id', type: 'string', format: 'uuid', example: 'b2c3d4e5-f6a7-8901-2345-67890abcdef1'),
        new OA\Property(property: 'status', type: 'string', enum: ['draft', 'published', 'archived'], example: 'published'),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true, example: '2023-10-27T10:00:00.000000Z'),
        new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string'), example: ['laravel', 'php', 'web development']),
    ],
    type: 'object'
)]
class BlogPostSchema
{
}

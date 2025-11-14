<?php

declare(strict_types=1);

namespace App\Application\Queries\Blog;

use App\Application\Handlers\Blog\GetBlogPostBySlugHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(GetBlogPostBySlugHandler::class)]
final readonly class GetBlogPostBySlug implements Query
{
    public function __construct(
        public string $slug
    ) {
    }
}

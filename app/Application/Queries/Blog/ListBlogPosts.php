<?php

declare(strict_types=1);

namespace App\Application\Queries\Blog;

use App\Application\Handlers\Blog\ListBlogPostsHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(ListBlogPostsHandler::class)]
final readonly class ListBlogPosts implements Query
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public array $filters = []
    ) {
    }
}

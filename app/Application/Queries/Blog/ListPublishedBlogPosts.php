<?php

declare(strict_types=1);

namespace App\Application\Queries\Blog;

use App\Application\Handlers\Blog\ListPublishedBlogPostsHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(ListPublishedBlogPostsHandler::class)]
final readonly class ListPublishedBlogPosts implements Query
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public array $filters
    ) {
    }
}

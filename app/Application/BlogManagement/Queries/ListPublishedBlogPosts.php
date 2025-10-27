<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Queries;

use App\Application\BlogManagement\Handlers\ListPublishedBlogPostsHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(ListPublishedBlogPostsHandler::class)]
final readonly class ListPublishedBlogPosts implements Query
{
    public function __construct(
        public int $limit = 10,
        public int $offset = 0
    ) {
    }
}


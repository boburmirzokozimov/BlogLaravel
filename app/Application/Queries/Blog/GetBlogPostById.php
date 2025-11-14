<?php

declare(strict_types=1);

namespace App\Application\Queries\Blog;

use App\Application\Handlers\Blog\GetBlogPostByIdHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(GetBlogPostByIdHandler::class)]
final readonly class GetBlogPostById implements Query
{
    public function __construct(
        public string $postId
    ) {
    }
}

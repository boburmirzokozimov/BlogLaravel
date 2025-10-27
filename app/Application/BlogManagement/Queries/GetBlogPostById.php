<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Queries;

use App\Application\BlogManagement\Handlers\GetBlogPostByIdHandler;
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


<?php

declare(strict_types=1);

namespace App\Application\Commands\Blog;

use App\Application\Handlers\Blog\UnpublishBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(UnpublishBlogPostHandler::class)]
final readonly class UnpublishBlogPost implements Command
{
    public function __construct(
        public string $postId
    ) {
    }
}

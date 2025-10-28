<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Commands;

use App\Application\BlogManagement\Handlers\PublishBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(PublishBlogPostHandler::class)]
final readonly class PublishBlogPost implements Command
{
    public function __construct(
        public string $postId
    ) {
    }
}

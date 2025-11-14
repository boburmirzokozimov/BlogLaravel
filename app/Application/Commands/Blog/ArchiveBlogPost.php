<?php

declare(strict_types=1);

namespace App\Application\Commands\Blog;

use App\Application\Handlers\Blog\ArchiveBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(ArchiveBlogPostHandler::class)]
final readonly class ArchiveBlogPost implements Command
{
    public function __construct(
        public string $postId
    ) {
    }
}

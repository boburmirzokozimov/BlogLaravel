<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Commands;

use App\Application\BlogManagement\Handlers\DeleteBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(DeleteBlogPostHandler::class)]
final readonly class DeleteBlogPost implements Command
{
    public function __construct(
        public string $postId
    ) {
    }
}

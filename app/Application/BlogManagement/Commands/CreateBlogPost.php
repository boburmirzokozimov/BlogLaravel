<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Commands;

use App\Application\BlogManagement\Handlers\CreateBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(CreateBlogPostHandler::class)]
final readonly class CreateBlogPost implements Command
{
    public function __construct(
        public string $title,
        public string $content,
        public string $authorId,
        public ?string $slug = null,
        public array $tags = []
    ) {
    }
}

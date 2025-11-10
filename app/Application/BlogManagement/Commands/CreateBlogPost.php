<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Commands;

use App\Application\BlogManagement\Handlers\CreateBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(CreateBlogPostHandler::class)]
final readonly class CreateBlogPost implements Command
{
    /**
     * @param string $title
     * @param string $content
     * @param string $authorId
     * @param string|null $slug
     * @param array<string> $tags
     */
    public function __construct(
        public string $title,
        public string $content,
        public string $authorId,
        public ?string $slug = null,
        public array $tags = []
    ) {
    }
}

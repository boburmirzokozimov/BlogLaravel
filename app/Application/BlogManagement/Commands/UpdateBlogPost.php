<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Commands;

use App\Application\BlogManagement\Handlers\UpdateBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(UpdateBlogPostHandler::class)]
final readonly class UpdateBlogPost implements Command
{
    /**
     * @param string $postId
     * @param string $title
     * @param string $content
     * @param string|null $slug
     * @param array<string> $tags
     */
    public function __construct(
        public string $postId,
        public string $title,
        public string $content,
        public ?string $slug = null,
        public array $tags = []
    ) {
    }
}

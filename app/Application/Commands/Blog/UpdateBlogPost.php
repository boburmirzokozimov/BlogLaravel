<?php

declare(strict_types=1);

namespace App\Application\Commands\Blog;

use App\Application\Handlers\Blog\UpdateBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(UpdateBlogPostHandler::class)]
final readonly class UpdateBlogPost implements Command
{
    /**
     * @param  array<string>  $tags
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

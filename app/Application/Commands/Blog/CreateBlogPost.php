<?php

declare(strict_types=1);

namespace App\Application\Commands\Blog;

use App\Application\Handlers\Blog\CreateBlogPostHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(CreateBlogPostHandler::class)]
final readonly class CreateBlogPost implements Command
{
    /**
     * @param  array<string>  $tags
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

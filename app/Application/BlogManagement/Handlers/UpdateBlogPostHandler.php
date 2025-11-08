<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Commands\UpdateBlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateBlogPostHandler implements CommandHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof UpdateBlogPost) {
            throw new InvalidArgumentException(
                sprintf(
                    'UpdateBlogPostHandler expects %s, got %s',
                    UpdateBlogPost::class,
                    get_class($command)
                )
            );
        }

        $post = $this->repository->findById(Id::fromString($command->postId));

        if (!$post) {
            throw new RuntimeException("Blog post not found: {$command->postId}");
        }

        $post->update(
            title: Title::new($command->title),
            content: Content::fromString($command->content),
            slug: $command->slug ? Slug::fromString($command->slug) : null
        );

        $post->setTags($command->tags);

        $this->repository->create($post);

        return null;
    }
}

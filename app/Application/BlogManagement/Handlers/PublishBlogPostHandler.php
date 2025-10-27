<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Commands\PublishBlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use RuntimeException;

final readonly class PublishBlogPostHandler implements CommandHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof PublishBlogPost) {
            throw new InvalidArgumentException(
                sprintf(
                    'PublishBlogPostHandler expects %s, got %s',
                    PublishBlogPost::class,
                    get_class($command)
                )
            );
        }

        $post = $this->repository->findById(Id::fromString($command->postId));

        if (!$post) {
            throw new RuntimeException("Blog post not found: {$command->postId}");
        }

        $post->publish();

        $this->repository->save($post);

        return null;
    }
}


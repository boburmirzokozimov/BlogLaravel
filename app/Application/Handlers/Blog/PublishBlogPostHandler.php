<?php

declare(strict_types=1);

namespace App\Application\Handlers\Blog;

use App\Application\Commands\Blog\PublishBlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

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
            throw new NotFound('Blog post', $command->postId);
        }

        $post->publish();

        $this->repository->save($post);

        return null;
    }
}

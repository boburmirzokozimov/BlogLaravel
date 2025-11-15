<?php

declare(strict_types=1);

namespace App\Application\Handlers\Blog;

use App\Application\Commands\Blog\UnpublishBlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class UnpublishBlogPostHandler implements CommandHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof UnpublishBlogPost) {
            throw new InvalidArgumentException(
                sprintf(
                    'UnpublishBlogPostHandler expects %s, got %s',
                    UnpublishBlogPost::class,
                    get_class($command)
                )
            );
        }

        $post = $this->repository->findById(Id::fromString($command->postId));

        if (!$post) {
            throw new NotFound('Blog post', $command->postId);
        }

        $post->unpublish();

        $this->repository->save($post);

        return null;
    }
}

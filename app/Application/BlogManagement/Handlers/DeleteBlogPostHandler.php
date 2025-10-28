<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Commands\DeleteBlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class DeleteBlogPostHandler implements CommandHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof DeleteBlogPost) {
            throw new InvalidArgumentException(
                sprintf(
                    'DeleteBlogPostHandler expects %s, got %s',
                    DeleteBlogPost::class,
                    get_class($command)
                )
            );
        }

        $this->repository->delete(Id::fromString($command->postId));

        return null;
    }
}

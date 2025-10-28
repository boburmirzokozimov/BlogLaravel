<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Commands\ArchiveBlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Infrastructure\Blog\EloquentBlogPost;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use RuntimeException;

final readonly class ArchiveBlogPostHandler implements CommandHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Command $command): EloquentBlogPost
    {
        if (!$command instanceof ArchiveBlogPost) {
            throw new InvalidArgumentException(
                sprintf(
                    'ArchiveBlogPostHandler expects %s, got %s',
                    ArchiveBlogPost::class,
                    get_class($command)
                )
            );
        }

        $post = $this->repository->findById(Id::fromString($command->postId));

        if (!$post) {
            throw new RuntimeException("Blog post not found: {$command->postId}");
        }

        $post->archive();

        return $this->repository->save($post);
    }
}

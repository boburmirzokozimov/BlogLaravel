<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Commands\CreateBlogPost;
use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use InvalidArgumentException;

final readonly class CreateBlogPostHandler implements CommandHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof CreateBlogPost) {
            throw new InvalidArgumentException(
                sprintf(
                    'CreateBlogPostHandler expects %s, got %s',
                    CreateBlogPost::class,
                    get_class($command)
                )
            );
        }

        $post = BlogPost::create(
            title: Title::new($command->title),
            content: Content::fromString($command->content),
            authorId: AuthorId::fromString($command->authorId),
            slug: $command->slug ? Slug::fromString($command->slug) : null
        );

        if (!empty($command->tags)) {
            $post->setTags($command->tags);
        }

        $this->repository->save($post);

        return $post->id()->toString();
    }
}


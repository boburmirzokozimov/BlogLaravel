<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Queries\ListPublishedBlogPosts;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

final readonly class ListPublishedBlogPostsHandler implements QueryHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof ListPublishedBlogPosts) {
            throw new InvalidArgumentException(
                sprintf(
                    'ListPublishedBlogPostsHandler expects %s, got %s',
                    ListPublishedBlogPosts::class,
                    get_class($query)
                )
            );
        }

        return $this->repository->findPublished($query->limit, $query->offset);
    }
}


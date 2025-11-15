<?php

declare(strict_types=1);

namespace App\Application\Handlers\Blog;

use App\Application\Queries\Blog\ListBlogPosts;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

final readonly class ListBlogPostsHandler implements QueryHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof ListBlogPosts) {
            throw new InvalidArgumentException(
                sprintf(
                    'ListBlogPostsHandler expects %s, got %s',
                    ListBlogPosts::class,
                    get_class($query)
                )
            );
        }

        return $this->repository->index($query->filters);
    }
}

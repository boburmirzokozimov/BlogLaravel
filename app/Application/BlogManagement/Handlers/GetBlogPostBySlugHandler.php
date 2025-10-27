<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Queries\GetBlogPostBySlug;
use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;
use RuntimeException;

final readonly class GetBlogPostBySlugHandler implements QueryHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof GetBlogPostBySlug) {
            throw new InvalidArgumentException(
                sprintf(
                    'GetBlogPostBySlugHandler expects %s, got %s',
                    GetBlogPostBySlug::class,
                    get_class($query)
                )
            );
        }

        $post = $this->repository->findBySlug($query->slug);

        if (!$post) {
            throw new RuntimeException("Blog post not found: {$query->slug}");
        }

        return $post;
    }
}


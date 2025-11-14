<?php

declare(strict_types=1);

namespace App\Application\Handlers\Blog;

use App\Application\Queries\Blog\GetBlogPostBySlug;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Infrastructure\Blog\EloquentBlogPost;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use App\Shared\Exceptions\NotFound;
use InvalidArgumentException;

final readonly class GetBlogPostBySlugHandler implements QueryHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Query $query): EloquentBlogPost
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
            throw new NotFound('Blog post', $query->slug);
        }

        return $post;
    }
}

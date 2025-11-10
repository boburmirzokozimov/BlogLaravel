<?php

declare(strict_types=1);

namespace App\Application\BlogManagement\Handlers;

use App\Application\BlogManagement\Queries\GetBlogPostById;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class GetBlogPostByIdHandler implements QueryHandler
{
    public function __construct(
        private BlogPostRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof GetBlogPostById) {
            throw new InvalidArgumentException(
                sprintf(
                    'GetBlogPostByIdHandler expects %s, got %s',
                    GetBlogPostById::class,
                    get_class($query)
                )
            );
        }

        $post = $this->repository->findById(Id::fromString($query->postId));

        if (!$post) {
            throw new NotFound('Blog post', $query->postId);
        }

        return $post;
    }
}

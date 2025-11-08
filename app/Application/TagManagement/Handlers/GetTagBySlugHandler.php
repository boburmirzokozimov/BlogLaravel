<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Handlers;

use App\Application\TagManagement\Queries\GetTagBySlug;
use App\Domain\Blog\Repositories\TagRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;
use RuntimeException;

final readonly class GetTagBySlugHandler implements QueryHandler
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof GetTagBySlug) {
            throw new InvalidArgumentException(
                sprintf(
                    'GetTagBySlugHandler expects %s, got %s',
                    GetTagBySlug::class,
                    get_class($query)
                )
            );
        }

        $tag = $this->repository->findBySlug($query->slug);

        if (!$tag) {
            throw new RuntimeException("Tag not found with slug: {$query->slug}");
        }

        return $tag;
    }
}


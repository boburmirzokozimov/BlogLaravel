<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Handlers;

use App\Application\TagManagement\Queries\ListTags;
use App\Domain\Blog\Repositories\TagRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

final readonly class ListTagsHandler implements QueryHandler
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof ListTags) {
            throw new InvalidArgumentException(
                sprintf(
                    'ListTagsHandler expects %s, got %s',
                    ListTags::class,
                    get_class($query)
                )
            );
        }

        return $this->repository->index($query->filters);
    }
}

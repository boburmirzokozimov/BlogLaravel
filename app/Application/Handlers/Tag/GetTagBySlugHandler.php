<?php

declare(strict_types=1);

namespace App\Application\Handlers\Tag;

use App\Application\Queries\Tag\GetTagBySlug;
use App\Domain\Blog\Repositories\TagRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use App\Shared\Exceptions\NotFound;
use InvalidArgumentException;

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
            throw new NotFound('Tag', $query->slug);
        }

        return $tag;
    }
}

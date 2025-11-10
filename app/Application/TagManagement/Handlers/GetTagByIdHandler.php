<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Handlers;

use App\Application\TagManagement\Queries\GetTagById;
use App\Domain\Blog\Repositories\TagRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use RuntimeException;

final readonly class GetTagByIdHandler implements QueryHandler
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof GetTagById) {
            throw new InvalidArgumentException(
                sprintf(
                    'GetTagByIdHandler expects %s, got %s',
                    GetTagById::class,
                    get_class($query)
                )
            );
        }

        $tag = $this->repository->findById(Id::fromString($query->tagId));

        if (!$tag) {
            throw new RuntimeException("Tag not found: {$query->tagId}");
        }

        return $tag;
    }
}

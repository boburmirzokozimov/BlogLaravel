<?php

namespace App\Application\Handlers\User;

use App\Application\Queries\User\ListUsers;
use App\Infrastructure\User\EloquentUser;
use App\Shared\Attributes\Handles;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

#[Handles(ListUsers::class)]
final readonly class ListUsersHandler implements QueryHandler
{
    public function __invoke(Query $query): mixed
    {
        if (!$query instanceof ListUsers) {
            throw new InvalidArgumentException(
                sprintf(
                    'ListUsersHandler expects %s, got %s',
                    ListUsers::class,
                    get_class($query)
                )
            );
        }

        return EloquentUser::query()
            ->filterRequest($query->filters)
            ->simplePaginate($query->filters['per_page'] ?? 10);
    }
}

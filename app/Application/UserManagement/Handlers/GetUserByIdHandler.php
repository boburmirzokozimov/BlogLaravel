<?php

namespace App\Application\UserManagement\Handlers;

use App\Application\UserManagement\Queries\GetUserById;
use App\Infrastructure\User\EloquentUser;
use App\Shared\Attributes\Handles;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

#[Handles(GetUserById::class)]
final readonly class GetUserByIdHandler implements QueryHandler
{

    public function __invoke(Query $query): EloquentUser
    {
        if (!$query instanceof GetUserById) {
            throw new InvalidArgumentException(
                sprintf(
                    'GetUserByIdHandler expects %s, got %s',
                    GetUserById::class,
                    get_class($query)
                )
            );
        }

        return EloquentUser::where('id', $query->userId)->firstOrFail();
    }
}


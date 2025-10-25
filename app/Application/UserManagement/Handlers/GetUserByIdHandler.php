<?php

namespace App\Application\UserManagement\Handlers;

use App\Application\UserManagement\Queries\GetUserById;
use App\Infrastructure\User\User;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

final class GetUserByIdHandler implements QueryHandler
{
    public function __invoke(Query $query): mixed
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

        return User::findOrFail($query->userId);
    }
}


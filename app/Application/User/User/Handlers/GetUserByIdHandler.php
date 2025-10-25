<?php

namespace App\Application\User\User\Handlers;

use App\Application\User\User\Queries\GetUserById;
use App\Models\User;
use App\Shared\CQRS\Query;
use App\Shared\CQRS\QueryHandler;
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


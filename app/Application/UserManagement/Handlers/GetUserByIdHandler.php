<?php

namespace App\Application\UserManagement\Handlers;

use App\Application\UserManagement\Queries\GetUserById;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Shared\CQRS\Query\Query;
use App\Shared\CQRS\Query\QueryHandler;
use InvalidArgumentException;

final readonly class GetUserByIdHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(Query $query): User
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

        return $this->repository->getById($query->userId);
    }
}


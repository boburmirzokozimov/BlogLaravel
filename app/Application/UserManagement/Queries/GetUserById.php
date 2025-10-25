<?php

namespace App\Application\UserManagement\Queries;

use App\Shared\CQRS\Query\Query;

final readonly class GetUserById implements Query
{
    public function __construct(
        public int $userId,
    )
    {
    }
}


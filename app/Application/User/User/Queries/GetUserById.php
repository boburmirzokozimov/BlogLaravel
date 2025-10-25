<?php

namespace App\Application\User\User\Queries;

use App\Shared\CQRS\Query;

final readonly class GetUserById implements Query
{
    public function __construct(
        public int $userId,
    )
    {
    }
}


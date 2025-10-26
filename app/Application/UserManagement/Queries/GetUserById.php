<?php

namespace App\Application\UserManagement\Queries;

use App\Application\UserManagement\Handlers\GetUserByIdHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(GetUserByIdHandler::class)]
final readonly class GetUserById implements Query
{
    public function __construct(
        public string $userId,
    ) {
    }
}

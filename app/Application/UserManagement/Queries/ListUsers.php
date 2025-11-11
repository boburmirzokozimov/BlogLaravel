<?php

namespace App\Application\UserManagement\Queries;

use App\Application\UserManagement\Handlers\ListUsersHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(ListUsersHandler::class)]
final readonly class ListUsers implements Query
{
    public function __construct(
        public array $filters = []
    ) {
    }
}

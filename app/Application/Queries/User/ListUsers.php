<?php

namespace App\Application\Queries\User;

use App\Application\Handlers\User\ListUsersHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(ListUsersHandler::class)]
final readonly class ListUsers implements Query
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public array $filters = []
    ) {
    }
}

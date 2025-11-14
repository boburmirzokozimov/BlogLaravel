<?php

namespace App\Application\Queries\User;

use App\Application\Handlers\User\GetUserByIdHandler;
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

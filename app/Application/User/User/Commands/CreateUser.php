<?php

namespace App\Application\User\User\Commands;

use App\Application\User\User\Handlers\CreateUserHandler;
use App\Shared\CQRS\Attributes\Handler;
use App\Shared\CQRS\Command;

/**
 * @handler App\Application\User\Handlers\CreateUserHandler
 */
#[Handler(CreateUserHandler::class)]
final readonly class CreateUser implements Command
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    )
    {
    }
}


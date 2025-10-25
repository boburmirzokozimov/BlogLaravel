<?php

namespace App\Application\UserManagement\Commands;

use App\Application\UserManagement\Handlers\CreateUserHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

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


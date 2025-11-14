<?php

namespace App\Application\Commands\User;

use App\Application\Handlers\User\CreateUserHandler;
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
    ) {
    }
}

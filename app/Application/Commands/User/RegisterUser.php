<?php

namespace App\Application\Commands\User;

use App\Application\Handlers\User\RegisterUserHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

/**
 * @handler App\Application\Handlers\User\RegisterUserHandler
 */
#[Handler(RegisterUserHandler::class)]
final readonly class RegisterUser implements Command
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}

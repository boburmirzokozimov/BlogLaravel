<?php

namespace App\Application\Commands\User;

use App\Application\Handlers\User\UpdateUserHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(UpdateUserHandler::class)]
final readonly class UpdateUser implements Command
{
    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public ?string $password = null,
        public string $status = 'active',
    ) {
    }
}

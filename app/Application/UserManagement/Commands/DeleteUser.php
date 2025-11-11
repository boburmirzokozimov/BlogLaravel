<?php

namespace App\Application\UserManagement\Commands;

use App\Application\UserManagement\Handlers\DeleteUserHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(DeleteUserHandler::class)]
final readonly class DeleteUser implements Command
{
    public function __construct(
        public string $userId,
    ) {
    }
}

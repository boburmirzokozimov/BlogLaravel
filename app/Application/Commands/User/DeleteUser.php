<?php

namespace App\Application\Commands\User;

use App\Application\Handlers\User\DeleteUserHandler;
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

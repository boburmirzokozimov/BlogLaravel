<?php

namespace App\Application\Commands\User;

use App\Application\Handlers\User\AttachEmailToUserHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

/**
 * @handler App\Application\User\Handlers\AttachEmailToUserHandler
 */
#[Handler(AttachEmailToUserHandler::class)]
final readonly class AttachEmailToUser implements Command
{
    public function __construct(
        public string $email,
        public string $id,
    ) {
    }
}

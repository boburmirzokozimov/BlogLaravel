<?php

declare(strict_types=1);

namespace App\Application\Commands\User;

use App\Application\Handlers\User\VerifyEmailHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(VerifyEmailHandler::class)]
final readonly class VerifyEmail implements Command
{
    public function __construct(
        public string $token,
    ) {
    }
}

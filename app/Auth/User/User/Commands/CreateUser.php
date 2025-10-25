<?php

namespace App\Auth\User\User\Commands;

use App\Shared\CQRS\Command;

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


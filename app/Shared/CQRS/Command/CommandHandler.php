<?php

namespace App\Shared\CQRS\Command;

interface CommandHandler
{
    public function __invoke(Command $command): void;
}


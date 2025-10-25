<?php

namespace App\Shared\CQRS;

interface CommandHandler
{
    public function __invoke(Command $command): void;
}


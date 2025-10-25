<?php

namespace App\Shared\CQRS;

interface ICommandHandler
{
    public function __invoke(Command $command);
}

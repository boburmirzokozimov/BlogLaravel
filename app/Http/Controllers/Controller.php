<?php

namespace App\Http\Controllers;

use App\Shared\CQRS\Bus\CommandBus;
use App\Shared\CQRS\Bus\QueryBus;

abstract class Controller
{
    public function __construct(
        protected readonly CommandBus $commands,
        protected readonly QueryBus   $queries,
    )
    {
    }
}

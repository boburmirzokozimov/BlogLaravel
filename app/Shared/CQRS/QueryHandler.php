<?php

namespace App\Shared\CQRS;

interface QueryHandler
{
    public function __invoke(Query $query): mixed;
}


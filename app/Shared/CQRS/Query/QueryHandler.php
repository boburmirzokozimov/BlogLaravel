<?php

namespace App\Shared\CQRS\Query;

interface QueryHandler
{
    public function __invoke(Query $query): mixed;
}

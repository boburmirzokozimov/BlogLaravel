<?php

namespace App\Shared\CQRS;

interface IQueryHandler
{
    public function __invoke(IQuery $query);
}

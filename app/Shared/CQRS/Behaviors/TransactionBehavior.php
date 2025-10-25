<?php

namespace App\Shared\CQRS\Behaviors;

use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Query\Query;
use Closure;
use Illuminate\Support\Facades\DB;

class TransactionBehavior implements Behavior
{
    public function handle(Command|Query $message, Closure $next): mixed
    {
        return DB::transaction(fn() => $next($message));
    }
}

<?php

namespace App\Shared\CQRS\Behaviors;

use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Query\Query;
use Closure;
use Illuminate\Support\Facades\Log;

class LoggingBehavior implements Behavior
{

    public function handle(Command|Query $message, Closure $next): mixed
    {
        Log::info('[CQRS] Handling ' . $message::class, ['message' => (array)$message]);
        $result = $next($message);
        Log::info('[CQRS] Handled ' . $message::class);
        return $result;
    }
}

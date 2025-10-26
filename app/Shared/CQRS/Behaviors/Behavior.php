<?php

declare(strict_types=1);

namespace App\Shared\CQRS\Behaviors;

use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Query\Query;
use Closure;

/**
 * A pipeline behavior runs before/after the handler.
 * It MUST call $next($message) to continue the chain.
 */
interface Behavior
{
    /**
     * @param Command|Query $message
     * @param Closure $next receives the same $message and returns mixed
     * @return mixed
     */
    public function handle(Command|Query $message, Closure $next): mixed;
}

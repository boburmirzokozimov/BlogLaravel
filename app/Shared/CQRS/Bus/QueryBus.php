<?php

namespace App\Shared\CQRS\Bus;

use App\Shared\CQRS\Behaviors\Behavior;
use App\Shared\CQRS\HandlerLocator;
use App\Shared\CQRS\Query\Query;
use Illuminate\Contracts\Pipeline\Pipeline;

final readonly class QueryBus
{
    /** @param class-string<Behavior>[] $behaviors */
    public function __construct(
        private HandlerLocator $locator,
        private Pipeline $pipeline,
        private array $behaviors = []
    ) {
    }

    /**
     * Executes a query and returns its result.
     */
    public function ask(Query $query): mixed
    {
        $handler = $this->locator->forQuery($query);

        return $this->pipeline
            ->send($query)
            ->through($this->behaviors) // order matters
            ->then(fn ($msg) => $handler($msg));
    }
}

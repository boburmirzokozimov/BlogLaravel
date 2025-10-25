<?php

namespace App\Shared\CQRS;

use Illuminate\Contracts\Pipeline\Pipeline;

final readonly class CommandBus
{

    /** @param class-string<Behavior>[] $behaviors */
    public function __construct(
        private HandlerLocator $locator,
        private Pipeline       $pipeline,
        private array          $behaviors = []
    )
    {
    }

    /**
     * Dispatches a command to its handler.
     */
    public function dispatch(Command $command): void
    {
        $handler = $this->locator->forCommand($command);

        $this->pipeline
            ->send($command)
            ->through($this->behaviors)
            ->then(fn($msg) => $handler($msg));
    }
}


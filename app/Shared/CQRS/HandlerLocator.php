<?php

namespace App\Shared\CQRS;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

final readonly class HandlerLocator
{
    public function __construct(
        private Container          $container,
        private ConventionResolver $resolver
    )
    {
    }

    /**
     * Resolves and instantiates a CommandHandler for the given Command.
     * @throws BindingResolutionException
     */
    public function forCommand(Command $command): CommandHandler
    {
        $handlerClass = $this->resolver->resolveHandlerClass($command);

        $handler = $this->container->make($handlerClass);

        if (!$handler instanceof CommandHandler) {
            throw new RuntimeException(
                sprintf(
                    'Handler [%s] must implement [%s]',
                    $handlerClass,
                    CommandHandler::class
                )
            );
        }

        return $handler;
    }

    /**
     * Resolves and instantiates a QueryHandler for the given Query.
     * @throws BindingResolutionException
     */
    public function forQuery(Query $query): QueryHandler
    {
        $handlerClass = $this->resolver->resolveHandlerClass($query);

        $handler = $this->container->make($handlerClass);

        if (!$handler instanceof QueryHandler) {
            throw new RuntimeException(
                sprintf(
                    'Handler [%s] must implement [%s]',
                    $handlerClass,
                    QueryHandler::class
                )
            );
        }

        return $handler;
    }
}


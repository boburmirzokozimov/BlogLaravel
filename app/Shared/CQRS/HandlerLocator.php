<?php

namespace App\Shared\CQRS;

use App\Shared\CQRS\Attributes\Handler;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use ReflectionClass;
use ReflectionException;
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
     * @throws ReflectionException
     */
    public function forCommand(Command $command): CommandHandler
    {
        $handlerClass = $this->resolveHandlerFromAttributeOrConvention($command);

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
     * @throws ReflectionException
     */
    private function resolveHandlerFromAttributeOrConvention(Command|Query $entity): string
    {
        $ref = new ReflectionClass($entity::class);

        $attrs = $ref->getAttributes(Handler::class);
        if ($attrs) {
            $attr = $attrs[0]->newInstance();
            if (!class_exists($attr->class)) {
                throw new RuntimeException("Handler class [{$attr->class}] not found for message");
            }
            return $attr->class;
        }

        return $this->resolver->resolveHandlerClass($entity);
    }

    /**
     * Resolves and instantiates a QueryHandler for the given Query.
     * @throws BindingResolutionException|ReflectionException
     */
    public function forQuery(Query $query): QueryHandler
    {
        $handlerClass = $this->resolveHandlerFromAttributeOrConvention($query);

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


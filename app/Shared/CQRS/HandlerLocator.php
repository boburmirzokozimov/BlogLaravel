<?php
declare(strict_types=1);

namespace App\Shared\CQRS;

use App\Shared\CQRS\Attributes\Handler as HandlerAttr;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Container\Container;
use ReflectionClass;
use RuntimeException;

final readonly class HandlerLocator
{
    public function __construct(
        private Container          $container,
        private ConventionResolver $resolver,
        private CacheRepository    $cache
    )
    {
    }

    public function forCommand(Command $command): CommandHandler
    {
        $handlerClass = $this->resolveHandlerClass($command);
        $handler = $this->container->make($handlerClass);
        if (!$handler instanceof CommandHandler) {
            throw new RuntimeException("Resolved handler [$handlerClass] for [" . get_class($command) . "] must implement " . CommandHandler::class);
        }
        return $handler;
    }

    public function resolveHandlerClass(Command|Query $message): string
    {
        $messageClass = $message::class;
        $key = $this->cacheKeyFor($messageClass);

        return $this->cache->remember($key, 86400, function () use ($message, $messageClass) {
            $ref = new ReflectionClass($messageClass);
            $attrs = $ref->getAttributes(HandlerAttr::class);
            if ($attrs) {
                $class = $attrs[0]->newInstance()->class;
                if (!class_exists($class)) {
                    throw new RuntimeException("Handler class [$class] declared on [$messageClass] does not exist");
                }
                return $class;
            }
            return $this->resolver->resolveHandlerClass($message);
        });
    }

    private function cacheKeyFor(string $messageClass): string
    {
        return 'cqrs.handler.' . strtr($messageClass, ['\\' => '.']); // safe key
    }

    public function forQuery(Query $query): QueryHandler
    {
        $handlerClass = $this->resolveHandlerClass($query);
        $handler = $this->container->make($handlerClass);
        if (!$handler instanceof QueryHandler) {
            throw new RuntimeException("Resolved handler [$handlerClass] for [" . get_class($query) . "] must implement " . QueryHandler::class);
        }
        return $handler;
    }
}

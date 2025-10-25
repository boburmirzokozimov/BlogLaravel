<?php

namespace App\Providers;

use App\Shared\CQRS\Behaviors\LoggingBehavior;
use App\Shared\CQRS\Behaviors\TransactionBehavior;
use App\Shared\CQRS\Bus\CommandBus;
use App\Shared\CQRS\Bus\QueryBus;
use App\Shared\CQRS\ConventionResolver;
use App\Shared\CQRS\HandlerLocator;
use Illuminate\Cache\Repository;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\ServiceProvider;

class CqrsServiceProvider extends ServiceProvider
{
    /**
     * Register CQRS services.
     */
    public function register(): void
    {
        $this->app->singleton(ConventionResolver::class, fn() => new ConventionResolver());

        $this->app->singleton(HandlerLocator::class, fn($app) => new HandlerLocator(
            $app,
            $app->make(ConventionResolver::class),
            $app->make(Repository::class),
        ));

        $this->app->singleton(CommandBus::class, fn($app) => new CommandBus(
            $app->make(HandlerLocator::class),
            $app->make(Pipeline::class),
            [
                TransactionBehavior::class,
                LoggingBehavior::class,
            ]
        ));

        $this->app->singleton(QueryBus::class, fn($app) => new QueryBus(
            $app->make(HandlerLocator::class),
            $app->make(Pipeline::class),
            [
            ]
        ));
    }

    /**
     * Bootstrap CQRS services.
     */
    public function boot(): void
    {
        //
    }
}


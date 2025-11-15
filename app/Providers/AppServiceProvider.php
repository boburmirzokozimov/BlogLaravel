<?php

namespace App\Providers;

use App\Application\Events\UserRegisterEvent;
use App\Application\Listeners\UserRegisteredListener;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Services\CacheService;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Blog\EloquentTagRepository;
use App\Infrastructure\Services\LaravelCacheService;
use App\Infrastructure\User\EloquentUserRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(
            \App\Domain\Blog\Repositories\BlogPostRepository::class,
            \App\Infrastructure\Blog\EloquentBlogPostRepository::class
        );
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
        $this->app->bind(CacheService::class, LaravelCacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            UserRegisterEvent::class,
            UserRegisteredListener::class
        );
    }
}

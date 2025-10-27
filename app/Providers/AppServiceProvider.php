<?php

namespace App\Providers;

use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\User\EloquentUserRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}

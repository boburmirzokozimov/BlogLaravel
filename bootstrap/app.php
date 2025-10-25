<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/v1')
                ->group(base_path('routes/api_v1.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(ForceJsonResponseMiddleware::class);
        $middleware->prepend(SetLocaleMiddleware::class);
//        $middleware->alias([
//
//        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Throwable $e): JsonResponse {
            return match (true) {
                $e instanceof AuthenticationException => response()->json([
                    'error' => [
                        'ru' => __('errors.unauthenticated', [], 'ru'),
                        'en' => __('errors.unauthenticated', [], 'en')
                    ]
                ], 401),
            };
        });
    })->create();

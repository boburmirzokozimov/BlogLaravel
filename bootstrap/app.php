<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
        $exceptions->renderable(function (Throwable $e): ?JsonResponse {
            // Let Laravel handle validation exceptions normally (422 status)
            if ($e instanceof ValidationException) {
                return null;
            }

            // Get current environment
            $env = app()->environment();
            $isDevOrTesting = in_array($env, ['local', 'development', 'testing']);

            return match (true) {
                $e instanceof AuthenticationException => response()->json([
                    'error' => [
                        'ru' => __('errors.unauthenticated', [], 'ru'),
                        'en' => __('errors.unauthenticated', [], 'en')
                    ]
                ], 401),
                // JWT Token Expired
                $e instanceof TokenExpiredException => response()->json([
                    'error' => [
                        'ru' => __('errors.token_expired', [], 'ru'),
                        'en' => __('errors.token_expired', [], 'en')
                    ]
                ], 401),
                // JWT Token Invalid
                $e instanceof TokenInvalidException => response()->json([
                    'error' => [
                        'ru' => __('errors.token_invalid', [], 'ru'),
                        'en' => __('errors.token_invalid', [], 'en')
                    ]
                ], 401),
                // JWT Token Blacklisted
                $e instanceof TokenBlacklistedException => response()->json([
                    'error' => [
                        'ru' => __('errors.token_blacklisted', [], 'ru'),
                        'en' => __('errors.token_blacklisted', [], 'en')
                    ]
                ], 401),
                // Dev/Testing environment - show detailed error info
                $isDevOrTesting => response()->json([
                    'error' => [
                        'message' => $e->getMessage(),
                        'type' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]
                ], 400),
                // Default handler - use translations
                default => response()->json([
                    'error' => [
                        'ru' => __('errors.bad_request', [], 'ru'),
                        'en' => __('errors.bad_request', [], 'en')
                    ]
                ], 400),
            };
        });
    })->create();

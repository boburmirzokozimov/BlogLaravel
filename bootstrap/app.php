<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Middleware\SetLocaleMiddleware;
use App\Shared\Exceptions\DomainException;
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
            $env = app()->environment();
            $isDevOrTesting = in_array($env, ['local', 'development', 'testing']);

            // Handle ValidationException with standardized format
            if ($e instanceof ValidationException) {
                return response()->json([
                    'code' => 'VALIDATION_FAILED',
                    'message' => [
                        'en' => __('errors.validation_failed', [], 'en'),
                        'ru' => __('errors.validation_failed', [], 'ru'),
                    ],
                    'error' => $e->errors(),
                ], 422);
            }

            if ($e instanceof DomainException) {
                $response = $e->toArray();

                if ($isDevOrTesting) {
                    $response['debug'] = [
                        'type' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ];
                }

                return response()->json($response, $e->status());
            }

            return match (true) {
                $e instanceof AuthenticationException => response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => [
                        'en' => __('errors.unauthenticated', [], 'en'),
                        'ru' => __('errors.unauthenticated', [], 'ru'),
                    ]
                ], 401),
                
                $e instanceof TokenExpiredException => response()->json([
                    'code' => 'TOKEN_EXPIRED',
                    'message' => [
                        'en' => __('errors.token_expired', [], 'en'),
                        'ru' => __('errors.token_expired', [], 'ru'),
                    ]
                ], 401),
                
                $e instanceof TokenInvalidException => response()->json([
                    'code' => 'TOKEN_INVALID',
                    'message' => [
                        'en' => __('errors.token_invalid', [], 'en'),
                        'ru' => __('errors.token_invalid', [], 'ru'),
                    ]
                ], 401),
                
                $e instanceof TokenBlacklistedException => response()->json([
                    'code' => 'TOKEN_BLACKLISTED',
                    'message' => [
                        'en' => __('errors.token_blacklisted', [], 'en'),
                        'ru' => __('errors.token_blacklisted', [], 'ru'),
                    ]
                ], 401),
                
                $isDevOrTesting => response()->json([
                    'code' => 'BAD_REQUEST',
                    'message' => [
                        'en' => $e->getMessage(),
                        'ru' => $e->getMessage(),
                    ],
                    'debug' => [
                        'type' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]
                ], 400),
                
                default => response()->json([
                    'code' => 'BAD_REQUEST',
                    'message' => [
                        'en' => __('errors.bad_request', [], 'en'),
                        'ru' => __('errors.bad_request', [], 'ru'),
                    ]
                ], 400),
            };
        });
    })->create();

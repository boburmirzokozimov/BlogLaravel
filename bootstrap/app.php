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
                $validator = $e->validator;
                $translatedErrors = [];

                foreach ($validator->failed() as $field => $rules) {
                    $translatedErrors[$field] = [];

                    foreach ($rules as $rule => $parameters) {
                        $ruleName = \Illuminate\Support\Str::snake($rule);
                        $attribute = str_replace('_', ' ', $field);

                        // Determine the type for size-based rules
                        $type = 'string'; // default
                        if ($validator->hasRule($field, ['Numeric', 'Integer'])) {
                            $type = 'numeric';
                        } elseif ($validator->hasRule($field, ['Array'])) {
                            $type = 'array';
                        } elseif ($validator->hasRule($field, ['File', 'Image', 'Mimes', 'Mimetypes'])) {
                            $type = 'file';
                        }

                        // Build parameters array for translation with proper keys
                        $params = ['attribute' => $attribute];

                        // Map parameters based on the rule
                        if (in_array($rule, ['Min', 'Max', 'Size'])) {
                            $params[strtolower($rule)] = $parameters[0] ?? '';
                        } elseif ($rule === 'Between') {
                            $params['min'] = $parameters[0] ?? '';
                            $params['max'] = $parameters[1] ?? '';
                        } elseif ($rule === 'RequiredIf') {
                            $params['other'] = $parameters[0] ?? '';
                            $params['value'] = $parameters[1] ?? '';
                        } elseif ($rule === 'Same') {
                            $params['other'] = $parameters[0] ?? '';
                        } else {
                            // Generic parameter mapping
                            foreach ($parameters as $key => $value) {
                                $params[is_numeric($key) ? "param{$key}" : $key] = $value;
                            }
                        }

                        // Get translated messages
                        $messageEn = __("validation.{$ruleName}", $params, 'en');
                        $messageRu = __("validation.{$ruleName}", $params, 'ru');

                        // If the message is an array (like min, max, size), select the appropriate type
                        if (is_array($messageEn)) {
                            $messageEn = $messageEn[$type] ?? $messageEn['string'];
                        }
                        if (is_array($messageRu)) {
                            $messageRu = $messageRu[$type] ?? $messageRu['string'];
                        }

                        $translatedErrors[$field][] = [
                            'en' => $messageEn,
                            'ru' => $messageRu,
                        ];
                    }
                }

                return response()->json([
                    'code' => 'VALIDATION_FAILED',
                    'message' => [
                        'en' => __('errors.validation_failed', [], 'en'),
                        'ru' => __('errors.validation_failed', [], 'ru'),
                    ],
                    'error' => $translatedErrors,
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
                ], $e->getCode()),

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

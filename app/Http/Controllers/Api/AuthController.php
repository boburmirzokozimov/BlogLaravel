<?php

namespace App\Http\Controllers\Api;

use App\Application\UserManagement\Commands\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/register',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', minLength: 1, maxLength: 255, example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 6, example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful registration',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'User registered successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Пользователь успешно зарегистрирован'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Token'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Validation failed'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Ошибка валидации данных'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'object', example: ['email' => ['The email field is required.']]),
                    ]
                )
            ),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->commands->dispatch(
            new CreateUser(
                name: $data['name'],
                email: $data['email'],
                password: $data['password']
            )
        );

        $token = auth()->login($user);

        return ApiResponse::success(
            messageKey: 'messages.user_registered',
            data: TokenResource::fromToken($token)
        );
    }

    #[OA\Post(
        path: '/api/v1/login',
        summary: 'Login user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful login',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'User logged in successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Пользователь успешно вошел в систему'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Token'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Invalid credentials',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Необходима авторизация'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Validation failed'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Ошибка валидации данных'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'object', example: ['email' => ['The email field is required.']]),
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return ApiResponse::error(
                messageKey: 'errors.unauthenticated',
                statusCode: 401
            );
        }

        return ApiResponse::success(
            messageKey: 'messages.user_logged_in',
            data: TokenResource::fromToken($token)
        );
    }

    #[OA\Get(
        path: '/api/v1/me',
        summary: 'Get authenticated user information',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Operation completed successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Операция выполнена успешно'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/User'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Необходима авторизация'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function me(): JsonResponse
    {
        return ApiResponse::success(
            messageKey: 'messages.user_logged_in',
            data: new UserResource(auth()->user())
        );
    }

    #[OA\Post(
        path: '/api/v1/logout',
        summary: 'Logout user',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successfully logged out',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'User logged out successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Пользователь успешно вышел из системы'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'null', nullable: true),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Необходима авторизация'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function logout(): JsonResponse
    {
        auth()->logout();

        return ApiResponse::success(
            messageKey: 'messages.user_logged_out',
            data: null
        );
    }

    #[OA\Post(
        path: '/api/v1/refresh',
        summary: 'Refresh JWT token',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'New access token',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Token refreshed successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Токен успешно обновлен'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Token'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated - Invalid or expired token',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Необходима авторизация'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function refresh(): JsonResponse
    {
        return ApiResponse::success(
            messageKey: 'messages.token_refreshed',
            data: TokenResource::fromToken(auth()->refresh())
        );
    }
}

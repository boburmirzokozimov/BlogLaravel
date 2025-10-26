<?php

namespace App\Http\Controllers\Api;

use App\Application\UserManagement\Commands\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserResource;
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
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
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
                        new OA\Property(property: 'code', type: 'string', example: 'SUCCESS'),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'User registered successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Пользователь успешно зарегистрирован'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                                new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
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
                        new OA\Property(property: 'code', type: 'string', example: 'VALIDATION_FAILED'),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Validation failed'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Ошибка валидации данных'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'error',
                            type: 'object',
                            example: ['email' => ['The email field is required.']]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function register(RegisterRequest $request)
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
            data: TokenResource::fromToken($token),
            messageKey: 'messages.user_registered'
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
                        new OA\Property(property: 'code', type: 'string', example: 'SUCCESS'),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'User logged in successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Пользователь успешно вошел в систему'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                                new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'string', example: 'UNAUTHORIZED'),
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
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return ApiResponse::error(
                code: 'UNAUTHORIZED',
                messageKey: 'errors.unauthenticated',
                statusCode: 401
            );
        }

        return ApiResponse::success(
            data: TokenResource::fromToken($token),
            messageKey: 'messages.user_logged_in'
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
                        new OA\Property(property: 'code', type: 'string', example: 'SUCCESS'),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Operation completed successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Операция выполнена успешно'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '9d0e5c1e-5b0a-4b1a-8c9a-1e5b0a4b1a8c'),
                                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive', 'pending', 'suspended'], example: 'active'),
                                new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'string', example: 'UNAUTHENTICATED'),
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
    public function me()
    {
        return ApiResponse::success(
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
                        new OA\Property(property: 'code', type: 'string', example: 'SUCCESS'),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'User logged out successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Пользователь успешно вышел из системы'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'string', example: 'UNAUTHENTICATED'),
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
    public function logout()
    {
        auth()->logout();

        return ApiResponse::success(
            messageKey: 'messages.user_logged_out'
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
                        new OA\Property(property: 'code', type: 'string', example: 'SUCCESS'),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Token refreshed successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Токен успешно обновлен'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGc...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                                new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'string', example: 'UNAUTHENTICATED'),
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
    public function refresh()
    {
        return ApiResponse::success(
            data: TokenResource::fromToken(auth()->refresh()),
            messageKey: 'messages.token_refreshed'
        );
    }
}

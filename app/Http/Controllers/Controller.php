<?php

namespace App\Http\Controllers;

use App\Shared\CQRS\Bus\CommandBus;
use App\Shared\CQRS\Bus\QueryBus;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API documentation for Blog Laravel application using CQRS pattern with UUID-based entities',
    title: 'Blog Laravel API'
)]
#[OA\Server(
    url: 'http://localhost:80',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'Authentication endpoints'
)]
#[OA\Tag(
    name: 'Users',
    description: 'User management endpoints (CQRS pattern)'
)]
abstract class Controller
{
    public function __construct(
        protected readonly CommandBus $commands,
        protected readonly QueryBus $queries,
    ) {
    }
}

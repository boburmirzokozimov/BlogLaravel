<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UserManagement\Commands\CreateUser;
use App\Application\UserManagement\Queries\GetUserById;
use App\Http\Controllers\Controller;
use App\Shared\CQRS\Bus\CommandBus;
use App\Shared\CQRS\Bus\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $this->commandBus->dispatch(
            new CreateUser(
                name: $data['name'],
                email: $data['email'],
                password: $data['password']
            )
        );

        return response()->json([
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->queryBus->ask(new GetUserById($id));

        return response()->json([
            'data' => $user
        ]);
    }
}


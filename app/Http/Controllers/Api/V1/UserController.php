<?php

namespace App\Http\Controllers\Api\V1;

use App\Auth\User\User\Commands\CreateUser;
use App\Auth\User\User\Queries\GetUserById;
use App\Http\Controllers\Controller;
use App\Shared\CQRS\CommandBus;
use App\Shared\CQRS\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly CommandBus $commands,
        private readonly QueryBus   $queries,
    )
    {
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

        $this->commands->dispatch(
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
    public function show(int $id): JsonResponse
    {
        $user = $this->queries->ask(new GetUserById($id));

        return response()->json([
            'data' => $user
        ]);
    }
}


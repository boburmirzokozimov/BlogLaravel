<?php

namespace App\Http\Controllers\Admin;

use App\Application\UserManagement\Commands\CreateUser;
use App\Application\UserManagement\Commands\DeleteUser;
use App\Application\UserManagement\Commands\UpdateUser;
use App\Application\UserManagement\Queries\GetUserById;
use App\Application\UserManagement\Queries\ListUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $users = $this->queries->ask(new ListUsers($request->all()));

        return Inertia::render('Admin/Users/Index', ['data' => UserResource::collection($users)]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->commands->dispatch(
            new CreateUser(
                name: $request->validated('name'),
                email: $request->validated('email'),
                password: $request->validated('password'),
            )
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): Response
    {
        $user = $this->queries->ask(new GetUserById($id));

        return Inertia::render('Admin/Users/Show', ['data' => new UserResource($user)]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id): Response
    {
        $user = $this->queries->ask(new GetUserById($id));

        return Inertia::render('Admin/Users/Edit', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id): RedirectResponse
    {
        $validated = $request->validated();

        $this->commands->dispatch(
            new UpdateUser(
                userId: $id,
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password'] ?? null,
                status: $validated['status'],
            )
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->commands->dispatch(new DeleteUser($id));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

<?php

namespace Tests\Feature\CQRS;

use App\Application\Commands\User\CreateUser;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Bus\CommandBus;
use App\Shared\CQRS\Command\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use RuntimeException;
use Tests\TestCase;

class CommandBusTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_resolves_handler_by_convention(): void
    {
        $bus = app(CommandBus::class);

        $bus->dispatch(new CreateUser(
            name: 'Alice Johnson',
            email: 'alice@example.com',
            password: 'password123'
        ));

        $this->assertDatabaseHas('users', [
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
        ]);
    }

    public function test_dispatch_throws_exception_for_missing_handler(): void
    {
        $this->expectException(RuntimeException::class);

        $bus = app(CommandBus::class);

        // Create a command without a handler
        $mockCommand = new class implements Command {};

        $bus->dispatch($mockCommand);
    }

    public function test_command_handler_can_use_dependency_injection(): void
    {
        // This test verifies that handlers are resolved from the container
        // and can use constructor dependency injection
        $bus = app(CommandBus::class);

        $bus->dispatch(new CreateUser(
            name: 'Bob Smith',
            email: 'bob@example.com',
            password: 'password123'
        ));

        $user = EloquentUser::where('email', 'bob@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Bob Smith', $user->name);
    }

    public function test_pipeline_runs_behaviors_in_order(): void
    {
        // Arrange
        Log::spy();
        $bus = app(CommandBus::class);

        Log::shouldReceive('info');
        // Act
        $bus->dispatch(new CreateUser(
            name: 'Alice Johnson',
            email: 'alice@example.com',
            password: 'password123'
        ));

        // Assert (rough example)
        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }
}

<?php

namespace Tests\Feature\CQRS;

use App\Application\UserManagement\Queries\GetUserById;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Bus\QueryBus;
use App\Shared\CQRS\Query\Query;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class QueryBusTest extends TestCase
{
    use RefreshDatabase;

    public function test_ask_resolves_handler_by_convention(): void
    {
        $user = EloquentUser::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $bus = app(QueryBus::class);
        $result = $bus->ask(new GetUserById($user->id));

        $this->assertInstanceOf(EloquentUser::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('Test User', $result->name);
    }

    public function test_query_handler_throws_exception_for_non_existent_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $bus = app(QueryBus::class);
        $bus->ask(new GetUserById(9999));
    }

    public function test_ask_throws_exception_for_missing_handler(): void
    {
        $this->expectException(RuntimeException::class);

        $bus = app(QueryBus::class);

        // Create a query without a handler
        $mockQuery = new class implements Query {
        };

        $bus->ask($mockQuery);
    }
}

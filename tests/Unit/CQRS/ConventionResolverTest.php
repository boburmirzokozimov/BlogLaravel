<?php

namespace Tests\Unit\CQRS;

use App\Application\User\User\Commands\CreateUser;
use App\Application\User\User\Queries\GetUserById;
use App\Shared\CQRS\Command;
use App\Shared\CQRS\ConventionResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConventionResolverTest extends TestCase
{
    private ConventionResolver $resolver;

    public function test_resolves_command_handler_by_convention(): void
    {
        $command = new CreateUser('John Doe', 'john@example.com', 'password123');
        $handlerClass = $this->resolver->resolveHandlerClass($command);

        $this->assertEquals(
            'App\Application\User\User\Handlers\CreateUserHandler',
            $handlerClass
        );
    }

    public function test_resolves_query_handler_by_convention(): void
    {
        $query = new GetUserById(1);
        $handlerClass = $this->resolver->resolveHandlerClass($query);

        $this->assertEquals(
            'App\Application\User\User\Handlers\GetUserByIdHandler',
            $handlerClass
        );
    }

    public function test_throws_exception_when_handler_not_found(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Handler .* not found/');

        // Create a mock command that doesn't have a handler
        $mockCommand = new class implements Command {
        };

        $this->resolver->resolveHandlerClass($mockCommand);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ConventionResolver();
    }
}


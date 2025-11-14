<?php

namespace Tests\Unit\CQRS;

use App\Application\Commands\User\CreateUser;
use App\Application\Handlers\User\CreateUserHandler;
use App\Application\Handlers\User\GetUserByIdHandler;
use App\Application\Queries\User\GetUserById;
use App\Shared\CQRS\HandlerLocator;
use Tests\TestCase;

class HandlerLocatorTest extends TestCase
{
    private HandlerLocator $locator;

    public function test_locates_command_handler(): void
    {
        $command = new CreateUser('John Doe', 'john@example.com', 'password123');
        $handler = $this->locator->forCommand($command);

        $this->assertInstanceOf(CreateUserHandler::class, $handler);
    }

    public function test_locates_query_handler(): void
    {
        $query = new GetUserById(1);
        $handler = $this->locator->forQuery($query);

        $this->assertInstanceOf(GetUserByIdHandler::class, $handler);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->locator = app(HandlerLocator::class);
    }
}

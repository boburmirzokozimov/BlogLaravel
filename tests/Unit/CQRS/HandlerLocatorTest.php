<?php

namespace Tests\Unit\CQRS;

use App\Application\User\User\Commands\CreateUser;
use App\Application\User\User\Handlers\CreateUserHandler;
use App\Application\User\User\Handlers\GetUserByIdHandler;
use App\Application\User\User\Queries\GetUserById;
use App\Shared\CQRS\ConventionResolver;
use App\Shared\CQRS\HandlerLocator;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class HandlerLocatorTest extends TestCase
{
    private HandlerLocator $locator;
    private Container $container;

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
        $this->container = new Container();
        $cache = new CacheRepository(new ArrayStore());
        $resolver = new ConventionResolver();
        $this->locator = new HandlerLocator($this->container, $resolver, $cache);
    }
}


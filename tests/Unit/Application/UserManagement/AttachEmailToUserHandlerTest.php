<?php

namespace Tests\Unit\Application\UserManagement;

use App\Application\UserManagement\Commands\AttachEmailToUser;
use App\Application\UserManagement\Commands\CreateUser;
use App\Application\UserManagement\Handlers\AttachEmailToUserHandler;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\Status;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\User\EloquentUser;
use App\Shared\Exceptions\InvariantViolation;
use App\Shared\Exceptions\NotFound;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class AttachEmailToUserHandlerTest extends TestCase
{
    private UserRepository $userRepository;

    private AttachEmailToUserHandler $handler;

    public function test_can_attach_email_to_user(): void
    {
        $userId = UserId::generate();
        $user = User::create(
            'Test User',
            Email::fromString('old@example.com'),
            PasswordHash::fromPlain('password123')
        );

        $eloquentUser = new EloquentUser();
        $eloquentUser->id = $userId->toString();
        $eloquentUser->email = 'new@example.com';
        $eloquentUser->status = 'active';

        $this->userRepository
            ->shouldReceive('getById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->equals($userId)))
            ->andReturn($user);

        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->with($user)
            ->andReturn($eloquentUser);

        $command = new AttachEmailToUser('new@example.com', $userId->toString());
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentUser::class, $result);
        $this->assertEquals('new@example.com', $result->email);
        $this->assertEquals('active', $result->status);
    }

    public function test_throws_exception_when_user_not_found(): void
    {
        $userId = UserId::generate();

        $this->userRepository
            ->shouldReceive('getById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->equals($userId)))
            ->andReturn(null);

        $this->expectException(NotFound::class);

        $command = new AttachEmailToUser('new@example.com', $userId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_email_already_activated(): void
    {
        $userId = UserId::generate();
        $email = Email::fromString('test@example.com');
        $email->activate(); // Email is already active

        $user = User::reconstitute(
            $userId,
            'Test User',
            $email,
            PasswordHash::fromPlain('password123'),
            Status::pending()
        );

        $this->userRepository
            ->shouldReceive('getById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->equals($userId)))
            ->andReturn($user);

        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('email_has_been_activated_already');

        $command = new AttachEmailToUser('new@example.com', $userId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_user_already_activated(): void
    {
        $userId = UserId::generate();
        $user = User::reconstitute(
            $userId,
            'Test User',
            Email::fromString('test@example.com'),
            PasswordHash::fromPlain('password123'),
            Status::active() // User is already active
        );

        $this->userRepository
            ->shouldReceive('getById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->equals($userId)))
            ->andReturn($user);

        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('user_has_already_been_activated');

        $command = new AttachEmailToUser('new@example.com', $userId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $wrongCommand = new CreateUser('Test', 'test@example.com', 'password');
        ($this->handler)($wrongCommand);
    }

    public function test_activates_user_after_attaching_email(): void
    {
        $userId = UserId::generate();
        $user = User::create(
            'Test User',
            Email::fromString('old@example.com'),
            PasswordHash::fromPlain('password123')
        );

        // Before attaching email, user should be pending
        $this->assertTrue($user->status()->equals(Status::pending()));

        $eloquentUser = new EloquentUser();
        $eloquentUser->id = $userId->toString();
        $eloquentUser->email = 'new@example.com';
        $eloquentUser->status = 'active';

        $this->userRepository
            ->shouldReceive('getById')
            ->once()
            ->andReturn($user);

        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($savedUser) {
                // Verify user is activated after save
                return $savedUser instanceof User
                    && $savedUser->status()->equals(Status::active())
                    && $savedUser->email()->active();
            }))
            ->andReturn($eloquentUser);

        $command = new AttachEmailToUser('new@example.com', $userId->toString());
        $result = ($this->handler)($command);

        $this->assertEquals('active', $result->status);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->handler = new AttachEmailToUserHandler($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

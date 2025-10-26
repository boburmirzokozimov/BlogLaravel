<?php

namespace Tests\Unit\Domain\User;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\Status;
use App\Domain\User\ValueObjects\UserId;
use App\Shared\Exceptions\InvariantViolation;
use InvalidArgumentException;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_can_create_new_user(): void
    {
        $email = Email::fromString('test@example.com');
        $password = PasswordHash::fromPlain('password123');

        $user = User::create('John Doe', $email, $password);

        $this->assertNotNull($user->id());
        $this->assertEquals('John Doe', $user->name());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertTrue($user->status()->equals(Status::pending()));
    }

    public function test_can_reconstitute_user_from_persistence(): void
    {
        $id = UserId::generate();
        $email = Email::fromString('test@example.com');
        $password = PasswordHash::fromPlain('password123');
        $status = Status::active();

        $user = User::reconstitute($id, 'Jane Doe', $email, $password, $status);

        $this->assertEquals($id, $user->id());
        $this->assertEquals('Jane Doe', $user->name());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertTrue($user->status()->equals(Status::active()));
    }

    public function test_can_rename_user(): void
    {
        $user = $this->createTestUser();

        $user->rename('New Name');

        $this->assertEquals('New Name', $user->name());
    }

    public function test_rename_throws_exception_when_name_too_short(): void
    {
        $user = $this->createTestUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User name too short');

        $user->rename('ab');
    }

    public function test_can_attach_email_to_inactive_email(): void
    {
        $user = User::create(
            'Test User',
            Email::fromString('old@example.com'),
            PasswordHash::fromPlain('password123')
        );

        $user->attachEmail('new@example.com');

        $this->assertEquals('new@example.com', $user->email()->value());
        $this->assertTrue($user->email()->active());
    }

    public function test_attach_email_throws_exception_when_email_already_active(): void
    {
        $email = Email::fromString('test@example.com');
        $email->activate();

        $user = User::create(
            'Test User',
            $email,
            PasswordHash::fromPlain('password123')
        );

        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('email_has_been_activated_already');

        $user->attachEmail('new@example.com');
    }

    public function test_can_activate_user(): void
    {
        $user = User::create(
            'Test User',
            Email::fromString('test@example.com'),
            PasswordHash::fromPlain('password123')
        );

        $this->assertTrue($user->status()->equals(Status::pending()));

        $user->activate();

        $this->assertTrue($user->status()->equals(Status::active()));
    }

    public function test_activate_throws_exception_when_user_already_active(): void
    {
        $user = User::reconstitute(
            UserId::generate(),
            'Test User',
            Email::fromString('test@example.com'),
            PasswordHash::fromPlain('password123'),
            Status::active()
        );

        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('user_has_already_been_activated');

        $user->activate();
    }

    public function test_user_getters_return_correct_values(): void
    {
        $id = UserId::generate();
        $email = Email::fromString('test@example.com');
        $password = PasswordHash::fromPlain('password123');
        $status = Status::active();

        $user = User::reconstitute($id, 'Test User', $email, $password, $status);

        $this->assertEquals($id, $user->id());
        $this->assertEquals('Test User', $user->name());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertEquals($status, $user->status());
    }

    private function createTestUser(): User
    {
        return User::create(
            'Test User',
            Email::fromString('test@example.com'),
            PasswordHash::fromPlain('password123')
        );
    }
}

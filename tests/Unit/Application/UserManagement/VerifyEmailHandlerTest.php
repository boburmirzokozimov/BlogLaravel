<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UserManagement;

use App\Application\Commands\User\VerifyEmail;
use App\Application\Handlers\User\VerifyEmailHandler;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Command\Command;
use App\Shared\Exceptions\NotFound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Tests\TestCase;

class VerifyEmailHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_verify_email_with_valid_token(): void
    {
        $user = EloquentUser::factory()->unverified()->pending()->create();
        $token = 'test-verification-token-123';

        Cache::put("email_verification:{$token}", $user->id, now()->addDays(7));

        $handler = app(VerifyEmailHandler::class);
        $command = new VerifyEmail($token);

        $handler($command);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('active', $user->status);
        $this->assertNull(Cache::get("email_verification:{$token}"));
    }

    public function test_throws_exception_when_token_not_found(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('Verification token');

        $handler = app(VerifyEmailHandler::class);
        $command = new VerifyEmail('invalid-token');

        $handler($command);
    }

    public function test_throws_exception_when_user_not_found(): void
    {
        $token = 'test-token';
        // Use a valid UUID format but non-existent user
        $nonExistentUserId = '00000000-0000-0000-0000-000000000000';
        Cache::put("email_verification:{$token}", $nonExistentUserId, now()->addDays(7));

        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('User');

        $handler = app(VerifyEmailHandler::class);
        $command = new VerifyEmail($token);

        $handler($command);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $handler = app(VerifyEmailHandler::class);
        $mockCommand = new class implements Command {
        };

        $handler($mockCommand);
    }

    public function test_removes_token_from_cache_after_verification(): void
    {
        $user = EloquentUser::factory()->unverified()->pending()->create();
        $token = 'test-token-456';

        Cache::put("email_verification:{$token}", $user->id, now()->addDays(7));

        $handler = app(VerifyEmailHandler::class);
        $handler(new VerifyEmail($token));

        $this->assertNull(Cache::get("email_verification:{$token}"));
    }
}

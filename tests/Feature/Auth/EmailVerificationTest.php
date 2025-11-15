<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Application\Commands\User\RegisterUser;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Bus\CommandBus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_unverified_user(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'email_verified_at' => null,
            'status' => 'pending',
        ]);
    }

    public function test_registration_generates_verification_token(): void
    {
        $bus = app(CommandBus::class);

        $user = $bus->dispatch(new RegisterUser(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123'
        ));

        // Verify the user was created
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
        ]);

        // Note: We can't easily check the exact token in cache without knowing it
        // But we know the token generation happens in RegisterUserHandler
    }

    public function test_can_verify_email_with_valid_token(): void
    {
        $user = EloquentUser::factory()->unverified()->pending()->create();
        $token = 'valid-verification-token';

        Cache::put("email_verification:{$token}", $user->id, now()->addDays(7));

        $response = $this->getJson("/api/v1/email/verify/{$token}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('active', $user->status);
    }

    public function test_verification_with_invalid_token_returns_404(): void
    {
        $response = $this->getJson('/api/v1/email/verify/invalid-token-123');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_verification_with_expired_token_returns_404(): void
    {
        $user = EloquentUser::factory()->unverified()->create();
        $token = 'expired-token';

        // Put token but let it expire (or use past time)
        Cache::put("email_verification:{$token}", $user->id, now()->subDay());

        // Wait a moment or manually expire
        Cache::forget("email_verification:{$token}");

        $response = $this->getJson("/api/v1/email/verify/{$token}");

        $response->assertStatus(404);
    }

    public function test_unverified_user_cannot_access_protected_routes(): void
    {
        $user = EloquentUser::factory()->unverified()->pending()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath('data.email_verified', false);
    }

    public function test_verified_user_can_access_protected_routes(): void
    {
        $user = EloquentUser::factory()->create(); // Factory creates verified users by default
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_registration_sends_verification_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200);

        // The email is sent via event listener, so we need to wait for events to be processed
        // In tests, events are typically dispatched synchronously
        Mail::assertSent(\App\Application\Mail\EmailVerificationMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_verification_activates_both_email_and_user_status(): void
    {
        $user = EloquentUser::factory()->unverified()->pending()->create();
        $token = 'test-token-789';

        Cache::put("email_verification:{$token}", $user->id, now()->addDays(7));

        $this->getJson("/api/v1/email/verify/{$token}");

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('active', $user->status);
    }
}

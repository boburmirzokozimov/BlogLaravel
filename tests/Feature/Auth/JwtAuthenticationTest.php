<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class JwtAuthenticationTest extends TestCase
{
    /**
     * Example test using registeredUser() helper
     */
    public function test_user_can_access_protected_route_with_token(): void
    {
        // Create a registered user with JWT token
        $registeredUser = $this->registeredUser([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Access protected route with token
        $response = $this->withBearerToken($registeredUser->token)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
                'data' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'code' => 'SUCCESS',
                'data' => [
                    'email' => 'john@example.com',
                ],
            ]);
    }

    /**
     * Example test using actingAsRegisteredUser() helper
     */
    public function test_user_can_logout(): void
    {
        // Create and authenticate as user (automatically sets Bearer token)
        $user = $this->actingAsRegisteredUser([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        // Make authenticated request
        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
            ])
            ->assertJson([
                'code' => 'SUCCESS',
            ]);
    }

    /**
     * Test without authentication
     */
    public function test_protected_route_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
            ]);
    }

    /**
     * Test with invalid token
     */
    public function test_invalid_token_returns_error(): void
    {
        $response = $this->withBearerToken('invalid-token-here')
            ->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
            ]);
    }

    /**
     * Test user registration
     */
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
                'data' => ['access_token', 'token_type', 'expires_in'],
            ])
            ->assertJson([
                'code' => 'SUCCESS',
            ]);
    }

    /**
     * Test token refresh
     */
    public function test_user_can_refresh_token(): void
    {
        $registeredUser = $this->registeredUser();

        $response = $this->withBearerToken($registeredUser->token)
            ->postJson('/api/v1/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
                'data' => ['access_token', 'token_type', 'expires_in'],
            ])
            ->assertJson([
                'code' => 'SUCCESS',
            ]);
    }
}


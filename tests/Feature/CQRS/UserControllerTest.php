<?php

namespace Tests\Feature\CQRS;

use App\Infrastructure\User\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_via_cqrs(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_can_be_retrieved_via_cqrs(): void
    {
        $user = EloquentUser::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response = $this->getJson("/api/v1/users/{$user->id}");
        dd($response->json());
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                ]
            ]);
    }

    public function test_validation_fails_for_invalid_user_data(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_validation_fails_for_duplicate_email(): void
    {
        EloquentUser::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}


<?php

namespace Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_user_can_register_successfully(): void
    {
        $this->withoutExceptionHandling();

        $this->postJson('api/v1/register', [
            'email' => 'test@mail.com',
            'password' => "password",
            'password_confirmation' => "password",
            'name' => 'Name '
        ]);

        $this->assertDatabaseCount('users', 1);
    }
}

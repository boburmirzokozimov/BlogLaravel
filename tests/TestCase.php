<?php

namespace Tests;

use App\Infrastructure\User\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Create a registered user with JWT token.
     *
     * @param array $attributes
     * @return object{user: EloquentUser, token: string}
     */
    protected function registeredUser(array $attributes = []): object
    {
        $user = EloquentUser::factory()->create($attributes);
        $token = auth('api')->login($user);

        return (object) [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Create and authenticate as a user with JWT token.
     *
     * @param array $attributes
     * @return EloquentUser
     */
    protected function actingAsRegisteredUser(array $attributes = []): EloquentUser
    {
        $user = EloquentUser::factory()->create($attributes);
        $token = auth('api')->login($user);

        $this->withHeader('Authorization', 'Bearer '.$token);

        return $user;
    }

    /**
     * Set authorization header with bearer token.
     *
     * @param string $token
     * @return $this
     */
    protected function withBearerToken(string $token): self
    {
        return $this->withHeader('Authorization', 'Bearer '.$token);
    }
}

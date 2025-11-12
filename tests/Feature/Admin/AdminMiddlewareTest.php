<?php

namespace Tests\Feature\Admin;

use App\Infrastructure\User\EloquentUser;
use App\Role;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    /**
     * Test that unauthenticated users are redirected to admin login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test that regular users (non-admin) get 403 Forbidden.
     */
    public function test_regular_user_gets_403_forbidden(): void
    {
        $user = EloquentUser::factory()->create([
            'role' => Role::User,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test that admin users can access admin routes.
     */
    public function test_admin_user_can_access_admin_routes(): void
    {
        $admin = EloquentUser::factory()->admin()->create();

        $response = $this->actingAs($admin, 'web')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that isAdmin() method returns true for admin users.
     */
    public function test_is_admin_returns_true_for_admin_users(): void
    {
        $admin = EloquentUser::factory()->admin()->create();

        $this->assertTrue($admin->isAdmin());
    }

    /**
     * Test that isAdmin() method returns false for regular users.
     */
    public function test_is_admin_returns_false_for_regular_users(): void
    {
        $user = EloquentUser::factory()->create([
            'role' => Role::User,
        ]);

        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test that default role is user when creating a user.
     */
    public function test_default_role_is_user(): void
    {
        $user = EloquentUser::factory()->create();

        $this->assertEquals(Role::User, $user->role);
        $this->assertFalse($user->isAdmin());
    }
}


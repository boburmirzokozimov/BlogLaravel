<?php

declare(strict_types=1);

namespace Tests\Feature\Tag;

use App\Infrastructure\Blog\EloquentTag;
use Tests\TestCase;

class TagApiTest extends TestCase
{
    /**
     * Test listing tags (public endpoint).
     */
    public function test_can_list_tags(): void
    {
        EloquentTag::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
                'data' => [
                    '*' => ['id', 'name', 'slug'],
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test listing tags with pagination.
     */
    public function test_can_list_tags_with_pagination(): void
    {
        EloquentTag::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/tags?per_page=5&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
                'data',
            ])
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertIsArray($data);
    }

    /**
     * Test searching tags by name or slug.
     */
    public function test_can_search_tags(): void
    {
        EloquentTag::factory()->create(['name' => 'PHP', 'slug' => 'php']);
        EloquentTag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        EloquentTag::factory()->create(['name' => 'JavaScript', 'slug' => 'javascript']);

        $response = $this->getJson('/api/v1/tags?search=php');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertIsArray($data);
        if (count($data) > 0) {
            $this->assertStringContainsStringIgnoringCase('php', $data[0]['name'] ?? $data[0]['slug'] ?? '');
        }
    }

    /**
     * Test getting tag by ID (public endpoint).
     */
    public function test_can_get_tag_by_id(): void
    {
        $tag = EloquentTag::factory()->create([
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ]);

        $response = $this->getJson("/api/v1/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
                'data' => ['id', 'name', 'slug'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $tag->id,
                    'name' => 'Test Tag',
                    'slug' => 'test-tag',
                ],
            ]);
    }

    /**
     * Test getting tag by slug (public endpoint).
     */
    public function test_can_get_tag_by_slug(): void
    {
        $tag = EloquentTag::factory()->create([
            'name' => 'Laravel Framework',
            'slug' => 'laravel-framework',
        ]);

        $response = $this->getJson('/api/v1/tags/slug/laravel-framework');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
                'data' => ['id', 'name', 'slug'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Laravel Framework',
                    'slug' => 'laravel-framework',
                ],
            ]);
    }

    /**
     * Test getting non-existent tag by ID returns 404.
     */
    public function test_getting_nonexistent_tag_by_id_returns_404(): void
    {
        $nonExistentId = '00000000-0000-0000-0000-000000000000';

        $response = $this->getJson("/api/v1/tags/{$nonExistentId}");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
                'error',
            ])
            ->assertJson([
                'code' => 'NOT_FOUND',
            ]);
    }

    /**
     * Test getting non-existent tag by slug returns 404.
     */
    public function test_getting_nonexistent_tag_by_slug_returns_404(): void
    {
        $response = $this->getJson('/api/v1/tags/slug/non-existent-slug');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
                'error',
            ])
            ->assertJson([
                'code' => 'NOT_FOUND',
            ]);
    }

    /**
     * Test creating a tag (requires authentication).
     */
    public function test_can_create_tag_with_authentication(): void
    {
        $user = $this->actingAsRegisteredUser();

        $response = $this->postJson('/api/v1/tags', [
            'name' => 'New Tag',
            'slug' => 'new-tag',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
                'data' => ['id', 'name', 'slug'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'New Tag',
                    'slug' => 'new-tag',
                ],
            ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'New Tag',
            'slug' => 'new-tag',
        ]);
    }

    /**
     * Test creating a tag without slug (auto-generates from name).
     */
    public function test_can_create_tag_without_slug(): void
    {
        $user = $this->actingAsRegisteredUser();

        $response = $this->postJson('/api/v1/tags', [
            'name' => 'Auto Slug Tag',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
                'data' => ['id', 'name', 'slug'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Auto Slug Tag',
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data['slug']);
    }

    /**
     * Test creating a tag without authentication returns 401.
     */
    public function test_creating_tag_without_authentication_returns_401(): void
    {
        $response = $this->postJson('/api/v1/tags', [
            'name' => 'Unauthorized Tag',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
            ])
            ->assertJson([
                'code' => 'UNAUTHENTICATED',
            ]);
    }

    /**
     * Test creating a tag with invalid data returns validation errors.
     */
    public function test_creating_tag_with_invalid_data_returns_validation_errors(): void
    {
        $user = $this->actingAsRegisteredUser();

        $response = $this->postJson('/api/v1/tags', [
            'name' => 'AB', // Too short (min 3 characters)
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'code',
                'message' => ['en', 'ru'],
                'error',
            ])
            ->assertJson([
                'code' => 'VALIDATION_FAILED',
            ]);
    }

    /**
     * Test creating a tag with duplicate slug returns validation error.
     */
    public function test_creating_tag_with_duplicate_slug_returns_validation_error(): void
    {
        $user = $this->actingAsRegisteredUser();

        EloquentTag::factory()->create(['slug' => 'existing-slug']);

        $response = $this->postJson('/api/v1/tags', [
            'name' => 'New Tag',
            'slug' => 'existing-slug',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'code' => 'VALIDATION_FAILED',
            ]);
    }

    /**
     * Test updating a tag (requires authentication).
     */
    public function test_can_update_tag_with_authentication(): void
    {
        $user = $this->actingAsRegisteredUser();
        $tag = EloquentTag::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
        ]);

        $response = $this->putJson("/api/v1/tags/{$tag->id}", [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test updating a tag without slug (generates from name).
     */
    public function test_can_update_tag_without_slug(): void
    {
        $user = $this->actingAsRegisteredUser();
        $tag = EloquentTag::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
        ]);

        $response = $this->putJson("/api/v1/tags/{$tag->id}", [
            'name' => 'Updated Name Without Slug',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Name Without Slug',
        ]);
    }

    /**
     * Test updating a tag without authentication returns 401.
     */
    public function test_updating_tag_without_authentication_returns_401(): void
    {
        $tag = EloquentTag::factory()->create();

        $response = $this->putJson("/api/v1/tags/{$tag->id}", [
            'name' => 'Unauthorized Update',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'code' => 'UNAUTHENTICATED',
            ]);
    }

    /**
     * Test updating a non-existent tag returns 404.
     */
    public function test_updating_nonexistent_tag_returns_404(): void
    {
        $user = $this->actingAsRegisteredUser();
        $nonExistentId = '00000000-0000-0000-0000-000000000000';

        $response = $this->putJson("/api/v1/tags/{$nonExistentId}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'code' => 'NOT_FOUND',
            ]);
    }

    /**
     * Test updating a tag with invalid data returns validation errors.
     */
    public function test_updating_tag_with_invalid_data_returns_validation_errors(): void
    {
        $user = $this->actingAsRegisteredUser();
        $tag = EloquentTag::factory()->create();

        $response = $this->putJson("/api/v1/tags/{$tag->id}", [
            'name' => 'AB', // Too short
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'code' => 'VALIDATION_FAILED',
            ]);
    }

    /**
     * Test deleting a tag (requires authentication).
     */
    public function test_can_delete_tag_with_authentication(): void
    {
        $user = $this->actingAsRegisteredUser();
        $tag = EloquentTag::factory()->create();

        $response = $this->deleteJson("/api/v1/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message' => ['en', 'ru'],
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    /**
     * Test deleting a tag without authentication returns 401.
     */
    public function test_deleting_tag_without_authentication_returns_401(): void
    {
        $tag = EloquentTag::factory()->create();

        $response = $this->deleteJson("/api/v1/tags/{$tag->id}");

        $response->assertStatus(401)
            ->assertJson([
                'code' => 'UNAUTHENTICATED',
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
        ]);
    }

    /**
     * Test deleting a non-existent tag still returns success (idempotent delete).
     */
    public function test_deleting_nonexistent_tag_returns_success(): void
    {
        $user = $this->actingAsRegisteredUser();
        $nonExistentId = '00000000-0000-0000-0000-000000000000';

        $response = $this->deleteJson("/api/v1/tags/{$nonExistentId}");

        // Delete is idempotent - returns success even if tag doesn't exist
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}

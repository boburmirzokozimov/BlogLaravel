<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repositories;

use App\Domain\Blog\Entity\Tag;
use App\Infrastructure\Blog\EloquentTag;
use App\Shared\ValueObjects\Id;
use Illuminate\Pagination\LengthAwarePaginator;

interface TagRepository
{
    /**
     * Create a new tag.
     */
    public function create(Tag $tag): EloquentTag;

    /**
     * Save a tag (create or update).
     */
    public function save(Tag $tag): EloquentTag;

    /**
     * Find a tag by ID.
     */
    public function findById(Id $id): ?Tag;

    /**
     * Find a tag by slug.
     */
    public function findBySlug(string $slug): ?Tag;

    /**
     * Find a tag by name.
     */
    public function findByName(string $name): ?Tag;

    /**
     * Get all tags with optional filters.
     *
     * @param array<string, string> $filters
     * @return LengthAwarePaginator<int, EloquentTag>
     */
    public function index(array $filters = []): LengthAwarePaginator;

    /**
     * Delete a tag.
     */
    public function delete(Id $id): void;
}

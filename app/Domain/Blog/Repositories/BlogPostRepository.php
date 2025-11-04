<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repositories;

use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Shared\ValueObjects\Id;

interface BlogPostRepository
{
    /**
     * Save a blog post (create or update).
     */
    public function save(BlogPost $post): mixed;

    /**
     * Find a blog post by ID.
     */
    public function findById(Id $id): ?BlogPost;

    /**
     * Find a blog post by slug.
     */
    public function findBySlug(string $slug): mixed;

    /**
     * Get all blog posts by author.
     *
     * @return BlogPost[]
     */
    public function findByAuthor(AuthorId $authorId): mixed;

    /**
     * Get all published blog posts.
     *
     * @return BlogPost[]
     */
    public function index(array $filters = []): mixed;

    /**
     * Delete a blog post.
     */
    public function delete(Id $id): void;
}

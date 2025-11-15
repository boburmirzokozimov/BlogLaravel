<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repositories;

use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Infrastructure\Blog\EloquentBlogPost;
use App\Shared\ValueObjects\Id;
use Illuminate\Pagination\Paginator;

interface BlogPostRepository
{
    /**
     * Save a blog post (create or update).
     */
    public function create(BlogPost $post): mixed;

    public function save(BlogPost $post): mixed;

    /**
     * Find a blog post by ID.
     */
    public function findById(Id $id): ?BlogPost;

    public function findByIdEloquent(Id $id): ?EloquentBlogPost;

    /**
     * Find a blog post by slug.
     */
    public function findBySlug(string $slug): mixed;

    /**
     * Get all blog posts by author.
     *
     * @return Paginator<int, EloquentBlogPost>
     */
    public function findByAuthor(AuthorId $authorId): mixed;

    /**
     * @param array<string, string> $filters
     * @return Paginator<int, EloquentBlogPost>
     */
    public function index(array $filters = []): mixed;

    /**
     * Delete a blog post.
     */
    public function delete(Id $id): void;
}

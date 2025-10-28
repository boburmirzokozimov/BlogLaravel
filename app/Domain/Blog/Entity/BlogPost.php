<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity;

use App\Domain\Blog\ValueObjects\AuthorId;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\PostStatus;
use App\Domain\Blog\ValueObjects\PublishedAt;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\Exceptions\InvariantViolation;
use App\Shared\ValueObjects\Id;

final class BlogPost
{
    private Id $id;

    private Title $title;

    private Slug $slug;

    private Content $content;

    private AuthorId $authorId;

    private PostStatus $status;

    private ?PublishedAt $publishedAt;

    private array $tags = [];

    // domain events
    private array $events = [];

    private function __construct(
        Id $id,
        Title $title,
        Slug $slug,
        Content $content,
        AuthorId $authorId,
        PostStatus $status,
        ?PublishedAt $publishedAt = null,
        array $tags = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->content = $content;
        $this->authorId = $authorId;
        $this->status = $status;
        $this->publishedAt = $publishedAt;
        $this->tags = $tags;
    }

    /**
     * Create a new draft blog post.
     */
    public static function create(
        Title $title,
        Content $content,
        AuthorId $authorId,
        ?Slug $slug = null
    ): self {
        return new self(
            id: Id::generate(),
            title: $title,
            slug: $slug ?? Slug::fromTitle($title->getTitle()),
            content: $content,
            authorId: $authorId,
            status: PostStatus::draft(),
            publishedAt: null,
            tags: []
        );
    }

    /**
     * Reconstitute from persistence.
     */
    public static function reconstitute(
        Id $id,
        Title $title,
        Slug $slug,
        Content $content,
        AuthorId $authorId,
        PostStatus $status,
        ?PublishedAt $publishedAt,
        array $tags = []
    ): self {
        return new self(
            $id,
            $title,
            $slug,
            $content,
            $authorId,
            $status,
            $publishedAt,
            $tags
        );
    }

    // Getters
    public function id(): Id
    {
        return $this->id;
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function slug(): Slug
    {
        return $this->slug;
    }

    public function content(): Content
    {
        return $this->content;
    }

    public function authorId(): AuthorId
    {
        return $this->authorId;
    }

    public function status(): PostStatus
    {
        return $this->status;
    }

    public function publishedAt(): ?PublishedAt
    {
        return $this->publishedAt;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function isPublished(): bool
    {
        return $this->status->equals(PostStatus::published());
    }

    public function isDraft(): bool
    {
        return $this->status->equals(PostStatus::draft());
    }

    public function isArchived(): bool
    {
        return $this->status->equals(PostStatus::archived());
    }

    // Business Logic Methods

    /**
     * Update the blog post content and title.
     */
    public function update(Title $title, Content $content, ?Slug $slug = null): void
    {
        $this->title = $title;
        $this->content = $content;

        if ($slug !== null) {
            $this->slug = $slug;
        } else {
            // Auto-generate slug from new title
            $this->slug = Slug::fromTitle($title->getTitle());
        }
    }

    /**
     * Publish the blog post.
     */
    public function publish(): void
    {
        if ($this->isPublished()) {
            throw new InvariantViolation('Blog post is already published');
        }

        if ($this->isArchived()) {
            throw new InvariantViolation('Cannot publish an archived blog post');
        }

        $this->status = PostStatus::published();
        $this->publishedAt = PublishedAt::now();
    }

    /**
     * Archive the blog post.
     */
    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new InvariantViolation('Blog post is already archived');
        }

        $this->status = PostStatus::archived();
    }

    /**
     * Revert to draft status.
     */
    public function unpublish(): void
    {
        if ($this->isDraft()) {
            throw new InvariantViolation('Blog post is already a draft');
        }

        $this->status = PostStatus::draft();
        $this->publishedAt = null;
    }

    /**
     * Add a tag to the blog post.
     */
    public function addTag(string $tag): void
    {
        $normalizedTag = strtolower(trim($tag));

        if (in_array($normalizedTag, $this->tags, true)) {
            return; // Tag already exists
        }

        $this->tags[] = $normalizedTag;
    }

    /**
     * Remove a tag from the blog post.
     */
    public function removeTag(string $tag): void
    {
        $normalizedTag = strtolower(trim($tag));
        $this->tags = array_values(array_filter(
            $this->tags,
            fn ($t) => $t !== $normalizedTag
        ));
    }

    /**
     * Set all tags (replacing existing ones).
     */
    public function setTags(array $tags): void
    {
        $this->tags = array_values(array_unique(array_map(
            fn ($tag) => strtolower(trim($tag)),
            $tags
        )));
    }

    /**
     * Check if post has a specific tag.
     */
    public function hasTag(string $tag): bool
    {
        $normalizedTag = strtolower(trim($tag));

        return in_array($normalizedTag, $this->tags, true);
    }

    /**
     * Get domain events.
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}

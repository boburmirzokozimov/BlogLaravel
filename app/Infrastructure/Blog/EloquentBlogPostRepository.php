<?php

declare(strict_types=1);

namespace App\Infrastructure\Blog;

use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\PostStatus;
use App\Domain\Blog\ValueObjects\PublishedAt;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\ValueObjects\Id;

class EloquentBlogPostRepository implements BlogPostRepository
{
    public function save(BlogPost $post): void
    {
        EloquentBlogPost::updateOrCreate(
            ['id' => $post->id()->toString()],
            [
                'title' => $post->title()->getTitle(),
                'slug' => $post->slug()->value(),
                'content' => $post->content()->value(),
                'author_id' => $post->authorId()->toString(),
                'status' => $post->status()->value(),
                'published_at' => $post->publishedAt()?->toDateTime(),
                'tags' => $post->tags(),
            ]
        );
    }

    public function findById(Id $id): ?BlogPost
    {
        $record = EloquentBlogPost::find($id->toString());

        if (!$record) {
            return null;
        }

        return $this->toDomain($record);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        $record = EloquentBlogPost::where('slug', $slug)->first();

        if (!$record) {
            return null;
        }

        return $this->toDomain($record);
    }

    public function findByAuthor(AuthorId $authorId): array
    {
        $records = EloquentBlogPost::where('author_id', $authorId->toString())
            ->orderBy('created_at', 'desc')
            ->get();

        return $records->map(fn($record) => $this->toDomain($record))->all();
    }

    public function findPublished(int $limit = 10, int $offset = 0): array
    {
        $records = EloquentBlogPost::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $records->map(fn($record) => $this->toDomain($record))->all();
    }

    public function delete(Id $id): void
    {
        EloquentBlogPost::where('id', $id->toString())->delete();
    }

    /**
     * Convert Eloquent model to domain entity
     */
    private function toDomain(EloquentBlogPost $record): BlogPost
    {
        return BlogPost::reconstitute(
            id: Id::fromString($record->id),
            title: Title::new($record->title),
            slug: Slug::fromString($record->slug),
            content: Content::fromString($record->content),
            authorId: AuthorId::fromString($record->author_id),
            status: match ($record->status) {
                'draft' => PostStatus::draft(),
                'published' => PostStatus::published(),
                'archived' => PostStatus::archived(),
            },
            publishedAt: $record->published_at ? PublishedAt::fromDateTime($record->published_at) : null,
            tags: $record->tags ?? []
        );
    }
}


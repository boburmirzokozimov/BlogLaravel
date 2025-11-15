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
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use Illuminate\Pagination\Paginator;

class EloquentBlogPostRepository implements BlogPostRepository
{
    public function create(BlogPost $post): EloquentBlogPost
    {
        $model = new EloquentBlogPost;
        $model->id = $post->id()->toString();
        $this->mapDomainToModel($post, $model);
        $model->save();

        $model->tags()->sync($post->tags());

        return $model;
    }

    public function findById(Id $id): ?BlogPost
    {
        $record = EloquentBlogPost::find($id->toString());

        if (!$record) {
            return null;
        }

        return $this->toDomain($record);
    }

    public function findByIdEloquent(Id $id): ?EloquentBlogPost
    {
        $record = EloquentBlogPost::find($id->toString());

        if (!$record) {
            return null;
        }

        return $record;
    }

    public function findBySlug(string $slug): ?EloquentBlogPost
    {
        $record = EloquentBlogPost::where('slug', $slug)->first();

        if (!$record) {
            return null;
        }

        return $record;
    }

    /**
     * @return Paginator<int, EloquentBlogPost>
     */
    public function findByAuthor(AuthorId $authorId): Paginator
    {
        return EloquentBlogPost::where('author_id', $authorId->toString())
            ->orderBy('created_at', 'desc')
            ->simplePaginate();
    }

    /**
     * @param array<string, string|int> $filters
     * @return Paginator<int, EloquentBlogPost>
     */
    public function index(array $filters = []): Paginator
    {
        return EloquentBlogPost::query()
            ->orderBy('published_at', 'desc')
            ->filter($filters)
            ->simplePaginate($filters['per_page'] ?? 10);
    }

    public function delete(Id $id): void
    {
        EloquentBlogPost::where('id', $id->toString())->delete();
    }

    /**
     * Convert Eloquent model to domain entity.
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
                default => null,
            },
            publishedAt: $record->published_at ? PublishedAt::fromDateTime($record->published_at) : null,
            tags: $record->tags()->exists()
                ? $record->tags()->pluck('id')->all()
                : []
        );
    }

    public function save(BlogPost $post): EloquentBlogPost
    {
        $model = EloquentBlogPost::find($post->id()->toString());

        if (!$model) {
            throw new NotFound('Blog post', $post->id()->toString());
        }
        $this->mapDomainToModel($post, $model);
        $model->save();

        $model->tags()->sync($post->tags());

        return $model;
    }

    private function mapDomainToModel(BlogPost $post, EloquentBlogPost $model): void
    {
        $model->title = $post->title()->getTitle();
        $model->slug = $post->slug()->value();
        $model->content = $post->content()->value();
        $model->author_id = $post->authorId()->toString();
        $model->status = $post->status()->value();
        $model->published_at = $post->publishedAt()?->toDateTime();
    }
}

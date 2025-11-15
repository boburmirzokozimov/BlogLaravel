<?php

declare(strict_types=1);

namespace App\Infrastructure\Blog;

use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\ValueObjects\Id;
use Illuminate\Pagination\Paginator;

class EloquentTagRepository implements TagRepository
{
    public function create(Tag $tag): EloquentTag
    {
        $model = new EloquentTag;
        $model->id = $tag->getId()->toString();
        $model->name = $tag->getName()->getTitle();
        $model->slug = $tag->getSlug()->value();
        $model->save();

        return $model;
    }

    public function findById(Id $id): ?Tag
    {
        $record = EloquentTag::find($id->toString());

        if (!$record) {
            return null;
        }

        return $this->toDomain($record);
    }

    public function findBySlug(string $slug): ?Tag
    {
        $record = EloquentTag::where('slug', $slug)->first();

        if (!$record) {
            return null;
        }

        return $this->toDomain($record);
    }

    public function findByName(string $name): ?Tag
    {
        $record = EloquentTag::where('name', $name)->first();

        if (!$record) {
            return null;
        }

        return $this->toDomain($record);
    }

    /**
     * @param  array<string, string|int>  $filters
     * @return Paginator<int, EloquentTag>
     */
    public function index(array $filters = []): Paginator
    {
        return EloquentTag::query()
            ->filter($filters)
            ->orderBy('name', 'asc')
            ->simplePaginate($filters['per_page'] ?? 10);
    }

    public function delete(Id $id): void
    {
        EloquentTag::where('id', $id->toString())->delete();
    }

    public function save(Tag $tag): EloquentTag
    {
        $model = EloquentTag::find($tag->getId()->toString());

        if (!$model) {
            return $this->create($tag);
        }

        $model->name = $tag->getName()->getTitle();
        $model->slug = $tag->getSlug()->value();
        $model->save();

        return $model;
    }

    /**
     * Convert Eloquent model to domain entity.
     */
    private function toDomain(EloquentTag $record): Tag
    {
        return Tag::create(
            id: Id::fromString($record->id),
            name: Title::new($record->name),
            slug: Slug::fromString($record->slug)
        );
    }
}

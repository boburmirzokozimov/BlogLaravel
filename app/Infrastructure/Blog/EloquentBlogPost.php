<?php

declare(strict_types=1);

namespace App\Infrastructure\Blog;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $author_id
 * @property string $status
 * @property Carbon|null $published_at
 * @property array<string> $tags
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EloquentBlogPost extends Model
{
    protected $table = 'blog_posts';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'title',
        'slug',
        'content',
        'author_id',
        'status',
        'published_at',
        'tags',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'tags' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @param Builder<EloquentBlogPost> $builder
     * @param array<string, mixed> $filters
     * @return Builder<EloquentBlogPost>
     */
    #[Scope]
    protected function filter(Builder $builder, array $filters = []): Builder
    {
        return $builder->when(
            !empty($filters['author_id']),
            function (Builder $query) use ($filters) {
                $query->where('author_id', $filters['author_id']);
            }
        );
    }
}

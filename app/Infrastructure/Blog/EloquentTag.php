<?php

declare(strict_types=1);

namespace App\Infrastructure\Blog;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * Eloquent model representing a tag in the blog system.
 *
 * This model represents tags that can be associated with blog posts.
 * Tags are identified by a UUID string and have a name and slug.
 *
 * @property string $id The unique identifier (UUID) of the tag
 * @property string $name The name of the tag
 * @property string $slug The URL-friendly slug of the tag
 * @property Carbon $created_at The timestamp when the tag was created
 * @property Carbon $updated_at The timestamp when the tag was last updated
 */
class EloquentTag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * Defines how Eloquent should cast attributes when retrieving them from the database.
     *
     * @return array<string, string> An array mapping attribute names to their cast types
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the blog posts that belong to this tag.
     *
     * Defines a many-to-many relationship between tags and blog posts
     * through the `blog_post_tag` pivot table.
     * @return BelongsToMany<EloquentBlogPost, $this, Pivot>
     */
    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            EloquentBlogPost::class,
            'blog_post_tag',
            'tag_id',
            'blog_post_id'
        );
    }

    /**
     * Apply filters to the query builder.
     *
     * This scope method allows filtering tags based on various criteria.
     * Currently, supports filtering by search term (searches in name and slug).
     *
     * @param Builder<EloquentTag> $builder The query builder instance
     * @param array<string, mixed> $filters An associative array of filter criteria
     * @return Builder<EloquentTag> The modified query builder instance
     */
    #[Scope]
    protected function filter(Builder $builder, array $filters = []): Builder
    {
        return $builder->when(
            !empty($filters['search']),
            function (Builder $query) use ($filters) {
                $query->where(function (Builder $q) use ($filters) {
                    $q->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('slug', 'like', '%'.$filters['search'].'%');
                });
            }
        );
    }
}

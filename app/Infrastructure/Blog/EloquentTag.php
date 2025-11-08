<?php

declare(strict_types=1);

namespace App\Infrastructure\Blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EloquentTag extends Model
{
    protected $table = 'tags';

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
        'name',
        'slug',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
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
     * @return BelongsToMany<EloquentBlogPost>
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
}

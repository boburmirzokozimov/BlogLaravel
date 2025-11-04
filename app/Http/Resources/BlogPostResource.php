<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Infrastructure\Blog\EloquentBlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property EloquentBlogPost $resource
 */
class BlogPostResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'content' => $this->resource->content,
            'author_id' => $this->resource->author_id,
            'status' => $this->resource->status,
            'published_at' => $this->resource->published_at,
            'tags' => $this->resource->tags,
        ];
    }
}

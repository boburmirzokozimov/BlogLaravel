<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Blog\Entity\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property BlogPost $resource
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
            'id' => $this->resource->id()->toString(),
            'title' => $this->resource->title()->getTitle(),
            'slug' => $this->resource->slug()->value(),
            'content' => $this->resource->content()->value(),
            'excerpt' => $this->resource->content()->excerpt(),
            'word_count' => $this->resource->content()->wordCount(),
            'author_id' => $this->resource->authorId()->toString(),
            'status' => $this->resource->status()->value(),
            'published_at' => $this->resource->publishedAt()?->toIso8601(),
            'tags' => $this->resource->tags(),
            'is_published' => $this->resource->isPublished(),
            'is_draft' => $this->resource->isDraft(),
            'is_archived' => $this->resource->isArchived(),
        ];
    }
}

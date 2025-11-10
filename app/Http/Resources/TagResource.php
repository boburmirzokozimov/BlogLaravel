<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Blog\Entity\Tag;
use App\Infrastructure\Blog\EloquentTag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
        // Handle EloquentTag (from create/update operations)
        if ($this->resource instanceof EloquentTag) {
            return [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'slug' => $this->resource->slug,
                'created_at' => $this->resource->created_at?->toIso8601String(),
                'updated_at' => $this->resource->updated_at?->toIso8601String(),
            ];
        }

        // Handle Domain Tag (from queries)
        if ($this->resource instanceof Tag) {
            return [
                'id' => $this->resource->getId()->toString(),
                'name' => $this->resource->getName()->getTitle(),
                'slug' => $this->resource->getSlug()->value(),
            ];
        }

        // Handle paginated EloquentTag items
        if (is_array($this->resource) || (is_object($this->resource) && method_exists($this->resource, 'toArray'))) {
            return parent::toArray($request);
        }

        return [];
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Queries\Tag;

use App\Application\Handlers\Tag\ListTagsHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(ListTagsHandler::class)]
final readonly class ListTags implements Query
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public array $filters = []
    ) {
    }
}

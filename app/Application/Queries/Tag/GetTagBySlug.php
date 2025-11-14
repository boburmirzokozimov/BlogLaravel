<?php

declare(strict_types=1);

namespace App\Application\Queries\Tag;

use App\Application\Handlers\Tag\GetTagBySlugHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(GetTagBySlugHandler::class)]
final readonly class GetTagBySlug implements Query
{
    public function __construct(
        public string $slug
    ) {
    }
}

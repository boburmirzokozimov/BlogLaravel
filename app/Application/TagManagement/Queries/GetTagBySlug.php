<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Queries;

use App\Application\TagManagement\Handlers\GetTagBySlugHandler;
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


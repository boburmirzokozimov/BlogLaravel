<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Queries;

use App\Application\TagManagement\Handlers\GetTagByIdHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Query\Query;

#[Handler(GetTagByIdHandler::class)]
final readonly class GetTagById implements Query
{
    public function __construct(
        public string $tagId
    ) {
    }
}

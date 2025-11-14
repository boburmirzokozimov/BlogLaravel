<?php

declare(strict_types=1);

namespace App\Application\Commands\Tag;

use App\Application\Handlers\Tag\UpdateTagHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(UpdateTagHandler::class)]
final readonly class UpdateTag implements Command
{
    public function __construct(
        public string $tagId,
        public string $name,
        public ?string $slug = null
    ) {
    }
}

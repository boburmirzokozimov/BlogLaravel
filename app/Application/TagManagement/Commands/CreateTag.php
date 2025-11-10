<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Commands;

use App\Application\TagManagement\Handlers\CreateTagHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(CreateTagHandler::class)]
final readonly class CreateTag implements Command
{
    public function __construct(
        public string $name,
        public ?string $slug = null
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Application\TagManagement\Commands;

use App\Application\TagManagement\Handlers\DeleteTagHandler;
use App\Shared\Attributes\Handler;
use App\Shared\CQRS\Command\Command;

#[Handler(DeleteTagHandler::class)]
final readonly class DeleteTag implements Command
{
    public function __construct(
        public string $tagId
    ) {
    }
}


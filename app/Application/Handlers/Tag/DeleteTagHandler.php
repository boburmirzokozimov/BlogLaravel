<?php

declare(strict_types=1);

namespace App\Application\Handlers\Tag;

use App\Application\Commands\Tag\DeleteTag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class DeleteTagHandler implements CommandHandler
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof DeleteTag) {
            throw new InvalidArgumentException(
                sprintf(
                    'DeleteTagHandler expects %s, got %s',
                    DeleteTag::class,
                    get_class($command)
                )
            );
        }

        $this->repository->delete(Id::fromString($command->tagId));

        return null;
    }
}

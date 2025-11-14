<?php

declare(strict_types=1);

namespace App\Application\Handlers\Tag;

use App\Application\Commands\Tag\UpdateTag;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class UpdateTagHandler implements CommandHandler
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function __invoke(Command $command): mixed
    {
        if (!$command instanceof UpdateTag) {
            throw new InvalidArgumentException(
                sprintf(
                    'UpdateTagHandler expects %s, got %s',
                    UpdateTag::class,
                    get_class($command)
                )
            );
        }

        $existingTag = $this->repository->findById(Id::fromString($command->tagId));

        if (!$existingTag) {
            throw new NotFound('Tag', $command->tagId);
        }

        $updatedTag = Tag::create(
            id: $existingTag->getId(),
            name: Title::new($command->name),
            slug: Slug::fromTitle($command->slug ?? $command->name)
        );

        $this->repository->save($updatedTag);

        return null;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Handlers\Tag;

use App\Application\Commands\Tag\CreateTag;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Infrastructure\Blog\EloquentTag;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class CreateTagHandler implements CommandHandler
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function __invoke(Command $command): EloquentTag
    {
        if (!$command instanceof CreateTag) {
            throw new InvalidArgumentException(
                sprintf(
                    'CreateTagHandler expects %s, got %s',
                    CreateTag::class,
                    get_class($command)
                )
            );
        }

        $tag = Tag::create(
            id: Id::generate(),
            name: Title::new($command->name),
            slug: $command->slug ? Slug::fromString($command->slug) : Slug::fromTitle($command->name)
        );

        return $this->repository->create($tag);
    }
}

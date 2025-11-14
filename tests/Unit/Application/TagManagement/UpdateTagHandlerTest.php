<?php

namespace Tests\Unit\Application\TagManagement;

use App\Application\Commands\Tag\CreateTag;
use App\Application\Commands\Tag\UpdateTag;
use App\Application\Handlers\Tag\UpdateTagHandler;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class UpdateTagHandlerTest extends UnitTestCase
{
    private TagRepository $repository;

    private UpdateTagHandler $handler;

    public function test_can_update_tag(): void
    {
        $tagId = Id::generate();
        $existingTag = Tag::create(
            $tagId,
            Title::new('Original Name'),
            Slug::fromString('original-slug')
        );

        $newName = 'Updated Name';

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($tagId)))
            ->andReturn($existingTag);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($tag) use ($tagId, $newName) {
                return $tag instanceof Tag
                    && $tag->getId()->equals($tagId)
                    && $tag->getName()->getTitle() === $newName;
            }));

        $command = new UpdateTag($tagId->toString(), $newName);
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_can_update_tag_with_custom_slug(): void
    {
        $tagId = Id::generate();
        $existingTag = Tag::create(
            $tagId,
            Title::new('Original Name'),
            Slug::fromString('original-slug')
        );

        $newName = 'Updated Name';
        $newSlug = 'updated-slug';

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($existingTag);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($tag) use ($newSlug) {
                return $tag instanceof Tag
                    && $tag->getSlug()->value() === Slug::fromTitle($newSlug)->value();
            }));

        $command = new UpdateTag($tagId->toString(), $newName, $newSlug);
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_generates_slug_from_name_when_slug_not_provided(): void
    {
        $tagId = Id::generate();
        $existingTag = Tag::create(
            $tagId,
            Title::new('Original Name'),
            Slug::fromString('original-slug')
        );

        $newName = 'New Tag Name';

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($existingTag);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($tag) use ($newName) {
                return $tag instanceof Tag
                    && $tag->getSlug()->value() === Slug::fromTitle($newName)->value();
            }));

        $command = new UpdateTag($tagId->toString(), $newName);
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_preserves_tag_id_when_updating(): void
    {
        $tagId = Id::generate();
        $existingTag = Tag::create(
            $tagId,
            Title::new('Original Name'),
            Slug::fromString('original-slug')
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($existingTag);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($tag) use ($tagId) {
                return $tag instanceof Tag
                    && $tag->getId()->equals($tagId);
            }));

        $command = new UpdateTag($tagId->toString(), 'New Name');
        ($this->handler)($command);
    }

    public function test_throws_exception_when_tag_not_found(): void
    {
        $tagId = Id::generate();

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($tagId)))
            ->andReturn(null);

        $this->expectException(NotFound::class);

        $command = new UpdateTag($tagId->toString(), 'Name');
        ($this->handler)($command);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/UpdateTagHandler expects/');

        $wrongCommand = new CreateTag('Name');
        ($this->handler)($wrongCommand);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagRepository::class);
        $this->handler = new UpdateTagHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

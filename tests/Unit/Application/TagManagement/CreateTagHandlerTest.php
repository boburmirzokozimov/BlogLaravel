<?php

namespace Tests\Unit\Application\TagManagement;

use App\Application\TagManagement\Commands\CreateTag;
use App\Application\TagManagement\Commands\UpdateTag;
use App\Application\TagManagement\Handlers\CreateTagHandler;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Infrastructure\Blog\EloquentTag;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class CreateTagHandlerTest extends UnitTestCase
{
    private TagRepository $repository;

    private CreateTagHandler $handler;

    public function test_can_create_tag_without_slug(): void
    {
        $name = 'PHP';

        $eloquentTag = Mockery::mock(EloquentTag::class);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($tag) use ($name) {
                return $tag instanceof Tag
                    && $tag->getName()->getTitle() === $name
                    && $tag->getSlug()->value() === Slug::fromTitle($name)->value();
            }))
            ->andReturn($eloquentTag);

        $command = new CreateTag($name);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentTag::class, $result);
    }

    public function test_can_create_tag_with_custom_slug(): void
    {
        $name = 'PHP';
        $slug = 'custom-php-slug';

        $eloquentTag = Mockery::mock(EloquentTag::class);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($tag) use ($name, $slug) {
                return $tag instanceof Tag
                    && $tag->getName()->getTitle() === $name
                    && $tag->getSlug()->value() === $slug;
            }))
            ->andReturn($eloquentTag);

        $command = new CreateTag($name, $slug);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentTag::class, $result);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/CreateTagHandler expects/');

        $wrongCommand = new UpdateTag('tag-id', 'Name');
        ($this->handler)($wrongCommand);
    }

    public function test_generates_slug_from_name_when_slug_not_provided(): void
    {
        $name = 'Laravel Framework';

        $eloquentTag = Mockery::mock(EloquentTag::class);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($tag) use ($name) {
                return $tag instanceof Tag
                    && $tag->getName()->getTitle() === $name
                    && $tag->getSlug()->value() === Slug::fromTitle($name)->value();
            }))
            ->andReturn($eloquentTag);

        $command = new CreateTag($name);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentTag::class, $result);
    }

    public function test_creates_tag_with_generated_id(): void
    {
        $name = 'Test Tag';

        $eloquentTag = Mockery::mock(EloquentTag::class);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($tag) {
                return $tag instanceof Tag
                    && $tag->getId() instanceof Id;
            }))
            ->andReturn($eloquentTag);

        $command = new CreateTag($name);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentTag::class, $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagRepository::class);
        $this->handler = new CreateTagHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}


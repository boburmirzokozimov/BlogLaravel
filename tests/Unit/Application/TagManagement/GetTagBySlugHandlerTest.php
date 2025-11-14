<?php

namespace Tests\Unit\Application\TagManagement;

use App\Application\Handlers\Tag\GetTagBySlugHandler;
use App\Application\Queries\Tag\GetTagById;
use App\Application\Queries\Tag\GetTagBySlug;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class GetTagBySlugHandlerTest extends UnitTestCase
{
    private TagRepository $repository;

    private GetTagBySlugHandler $handler;

    public function test_can_get_tag_by_slug(): void
    {
        $slug = 'test-tag';
        $tag = Tag::create(
            Id::generate(),
            Title::new('Test Tag'),
            Slug::fromString($slug)
        );

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($slug)
            ->andReturn($tag);

        $query = new GetTagBySlug($slug);
        $result = ($this->handler)($query);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($slug, $result->getSlug()->value());
    }

    public function test_throws_exception_when_tag_not_found(): void
    {
        $slug = 'non-existent-slug';

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($slug)
            ->andReturn(null);

        $this->expectException(NotFound::class);

        $query = new GetTagBySlug($slug);
        ($this->handler)($query);
    }

    public function test_throws_exception_when_wrong_query_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/GetTagBySlugHandler expects/');

        $wrongQuery = new GetTagById('tag-id');
        ($this->handler)($wrongQuery);
    }

    public function test_returns_correct_tag_data(): void
    {
        $name = 'PHP';
        $slug = 'php-language';
        $tag = Tag::create(
            Id::generate(),
            Title::new($name),
            Slug::fromString($slug)
        );

        $this->repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with($slug)
            ->andReturn($tag);

        $query = new GetTagBySlug($slug);
        $result = ($this->handler)($query);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($name, $result->getName()->getTitle());
        $this->assertEquals($slug, $result->getSlug()->value());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagRepository::class);
        $this->handler = new GetTagBySlugHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

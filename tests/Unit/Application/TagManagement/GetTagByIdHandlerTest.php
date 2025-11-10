<?php

namespace Tests\Unit\Application\TagManagement;

use App\Application\TagManagement\Handlers\GetTagByIdHandler;
use App\Application\TagManagement\Queries\GetTagById;
use App\Application\TagManagement\Queries\GetTagBySlug;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repositories\TagRepository;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use App\Shared\Exceptions\NotFound;
use Tests\UnitTestCase;

class GetTagByIdHandlerTest extends UnitTestCase
{
    private TagRepository $repository;

    private GetTagByIdHandler $handler;

    public function test_can_get_tag_by_id(): void
    {
        $tagId = Id::generate();
        $tag = Tag::create(
            $tagId,
            Title::new('Test Tag'),
            Slug::fromString('test-tag')
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($tagId)))
            ->andReturn($tag);

        $query = new GetTagById($tagId->toString());
        $result = ($this->handler)($query);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertTrue($result->getId()->equals($tagId));
        $this->assertEquals('Test Tag', $result->getName()->getTitle());
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

        $query = new GetTagById($tagId->toString());
        ($this->handler)($query);
    }

    public function test_throws_exception_when_wrong_query_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/GetTagByIdHandler expects/');

        $wrongQuery = new GetTagBySlug('slug');
        ($this->handler)($wrongQuery);
    }

    public function test_returns_correct_tag_data(): void
    {
        $tagId = Id::generate();
        $name = 'Laravel';
        $slug = 'laravel-framework';
        $tag = Tag::create(
            $tagId,
            Title::new($name),
            Slug::fromString($slug)
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($tag);

        $query = new GetTagById($tagId->toString());
        $result = ($this->handler)($query);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($name, $result->getName()->getTitle());
        $this->assertEquals($slug, $result->getSlug()->value());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagRepository::class);
        $this->handler = new GetTagByIdHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

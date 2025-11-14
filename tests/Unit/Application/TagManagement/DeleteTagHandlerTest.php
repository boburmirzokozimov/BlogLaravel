<?php

namespace Tests\Unit\Application\TagManagement;

use App\Application\Commands\Tag\CreateTag;
use App\Application\Commands\Tag\DeleteTag;
use App\Application\Handlers\Tag\DeleteTagHandler;
use App\Domain\Blog\Repositories\TagRepository;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class DeleteTagHandlerTest extends UnitTestCase
{
    private TagRepository $repository;

    private DeleteTagHandler $handler;

    public function test_can_delete_tag(): void
    {
        $tagId = Id::generate();

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($tagId)));

        $command = new DeleteTag($tagId->toString());
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/DeleteTagHandler expects/');

        $wrongCommand = new CreateTag('Name');
        ($this->handler)($wrongCommand);
    }

    public function test_calls_repository_delete_with_correct_id(): void
    {
        $tagId = Id::generate();

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(function ($id) use ($tagId) {
                return $id instanceof Id && $id->equals($tagId);
            }));

        $command = new DeleteTag($tagId->toString());
        ($this->handler)($command);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagRepository::class);
        $this->handler = new DeleteTagHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

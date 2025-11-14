<?php

namespace Tests\Unit\Application\BlogManagement;

use App\Application\Commands\Blog\ArchiveBlogPost;
use App\Application\Commands\Blog\DeleteBlogPost;
use App\Application\Handlers\Blog\DeleteBlogPostHandler;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class DeleteBlogPostHandlerTest extends UnitTestCase
{
    private BlogPostRepository $repository;

    private DeleteBlogPostHandler $handler;

    public function test_can_delete_blog_post(): void
    {
        $postId = Id::generate();

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($postId)));

        $command = new DeleteBlogPost($postId->toString());
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_delete_is_called_with_correct_id(): void
    {
        $postId = Id::generate();
        $postIdString = $postId->toString();

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(function ($id) use ($postIdString) {
                return $id instanceof Id
                    && $id->toString() === $postIdString;
            }));

        $command = new DeleteBlogPost($postIdString);
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/DeleteBlogPostHandler expects/');

        $wrongCommand = new ArchiveBlogPost(Id::generate()->toString());
        ($this->handler)($wrongCommand);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(BlogPostRepository::class);
        $this->handler = new DeleteBlogPostHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

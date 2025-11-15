<?php

namespace Tests\Unit\Application\BlogManagement;

use App\Application\Commands\Blog\ArchiveBlogPost;
use App\Application\Commands\Blog\DeleteBlogPost;
use App\Application\Handlers\Blog\ArchiveBlogPostHandler;
use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\PostStatus;
use App\Domain\Blog\ValueObjects\PublishedAt;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Infrastructure\Blog\EloquentBlogPost;
use App\Shared\Exceptions\InvariantViolation;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class ArchiveBlogPostHandlerTest extends UnitTestCase
{
    private BlogPostRepository $repository;

    private ArchiveBlogPostHandler $handler;

    public function test_can_archive_draft_blog_post(): void
    {
        $postId = Id::generate();
        $post = BlogPost::create(
            Title::new('Test Post'),
            Content::fromString('Test content'),
            AuthorId::fromString(Id::generate()->toString())
        );

        $this->assertTrue($post->isDraft());

        $eloquentPost = Mockery::mock(EloquentBlogPost::class);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($postId)))
            ->andReturn($post);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($savedPost) {
                return $savedPost instanceof BlogPost
                    && $savedPost->isArchived();
            }))
            ->andReturn($eloquentPost);

        $command = new ArchiveBlogPost($postId->toString());
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentBlogPost::class, $result);
    }

    public function test_can_archive_published_blog_post(): void
    {
        $postId = Id::generate();
        $post = BlogPost::reconstitute(
            $postId,
            Title::new('Test Post'),
            Slug::fromString('test-post'),
            Content::fromString('Test content'),
            AuthorId::fromString(Id::generate()->toString()),
            PostStatus::published(),
            PublishedAt::now()
        );

        $this->assertTrue($post->isPublished());

        $eloquentPost = Mockery::mock(EloquentBlogPost::class);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($post);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($savedPost) {
                return $savedPost instanceof BlogPost
                    && $savedPost->isArchived();
            }))
            ->andReturn($eloquentPost);

        $command = new ArchiveBlogPost($postId->toString());
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentBlogPost::class, $result);
    }

    public function test_throws_exception_when_post_not_found(): void
    {
        $postId = Id::generate();

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($postId)))
            ->andReturn(null);

        $this->expectException(NotFound::class);

        $command = new ArchiveBlogPost($postId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_post_already_archived(): void
    {
        $postId = Id::generate();
        $post = BlogPost::reconstitute(
            $postId,
            Title::new('Test Post'),
            Slug::fromString('test-post'),
            Content::fromString('Test content'),
            AuthorId::fromString(Id::generate()->toString()),
            PostStatus::archived(),
            null
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($post);

        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('already archived');

        $command = new ArchiveBlogPost($postId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ArchiveBlogPostHandler expects/');

        $wrongCommand = new DeleteBlogPost(Id::generate()->toString());
        ($this->handler)($wrongCommand);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(BlogPostRepository::class);
        $this->handler = new ArchiveBlogPostHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

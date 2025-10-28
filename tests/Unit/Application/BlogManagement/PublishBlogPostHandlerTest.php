<?php

namespace Tests\Unit\Application\BlogManagement;

use App\Application\BlogManagement\Commands\DeleteBlogPost;
use App\Application\BlogManagement\Commands\PublishBlogPost;
use App\Application\BlogManagement\Handlers\PublishBlogPostHandler;
use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\PostStatus;
use App\Domain\Blog\ValueObjects\PublishedAt;
use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\Exceptions\InvariantViolation;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use RuntimeException;
use Tests\UnitTestCase;

class PublishBlogPostHandlerTest extends UnitTestCase
{
    private BlogPostRepository $repository;

    private PublishBlogPostHandler $handler;

    public function test_can_publish_draft_blog_post(): void
    {
        $postId = Id::generate();
        $post = BlogPost::create(
            Title::new('Test Post'),
            Content::fromString('Test content'),
            AuthorId::fromString(Id::generate()->toString())
        );

        $this->assertTrue($post->isDraft());

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
                    && $savedPost->isPublished()
                    && $savedPost->publishedAt() !== null;
            }));

        $command = new PublishBlogPost($postId->toString());
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_throws_exception_when_post_not_found(): void
    {
        $postId = Id::generate();

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($postId)))
            ->andReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Blog post not found');

        $command = new PublishBlogPost($postId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_post_already_published(): void
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

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($post);

        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('already published');

        $command = new PublishBlogPost($postId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_trying_to_publish_archived_post(): void
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
        $this->expectExceptionMessage('Cannot publish an archived blog post');

        $command = new PublishBlogPost($postId->toString());
        ($this->handler)($command);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/PublishBlogPostHandler expects/');

        $wrongCommand = new DeleteBlogPost(Id::generate()->toString());
        ($this->handler)($wrongCommand);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(BlogPostRepository::class);
        $this->handler = new PublishBlogPostHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

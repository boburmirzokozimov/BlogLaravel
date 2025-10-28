<?php

namespace Tests\Unit\Application\BlogManagement;

use App\Application\BlogManagement\Commands\CreateBlogPost;
use App\Application\BlogManagement\Commands\UpdateBlogPost;
use App\Application\BlogManagement\Handlers\CreateBlogPostHandler;
use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\PostStatus;
use App\Infrastructure\Blog\EloquentBlogPost;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class CreateBlogPostHandlerTest extends UnitTestCase
{
    private BlogPostRepository $repository;

    private CreateBlogPostHandler $handler;

    public function test_can_create_blog_post_without_slug(): void
    {
        $title = 'Test Blog Post';
        $content = 'This is test content';
        $authorId = Id::generate()->toString();

        $eloquentPost = Mockery::mock(EloquentBlogPost::class);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($post) use ($title, $content, $authorId) {
                return $post instanceof BlogPost
                    && $post->title()->getTitle() === $title
                    && $post->content()->value() === $content
                    && $post->authorId()->toString() === $authorId
                    && $post->isDraft()
                    && empty($post->tags());
            }))
            ->andReturn($eloquentPost);

        $command = new CreateBlogPost($title, $content, $authorId);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentBlogPost::class, $result);
    }

    public function test_can_create_blog_post_with_slug(): void
    {
        $title = 'Test Blog Post';
        $content = 'This is test content';
        $authorId = Id::generate()->toString();
        $slug = 'custom-slug';

        $eloquentPost = Mockery::mock(EloquentBlogPost::class);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($post) use ($slug) {
                return $post instanceof BlogPost
                    && $post->slug()->value() === $slug;
            }))
            ->andReturn($eloquentPost);

        $command = new CreateBlogPost($title, $content, $authorId, $slug);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentBlogPost::class, $result);
    }

    public function test_can_create_blog_post_with_tags(): void
    {
        $title = 'Test Blog Post';
        $content = 'This is test content';
        $authorId = Id::generate()->toString();
        $tags = ['php', 'laravel', 'testing'];

        $eloquentPost = Mockery::mock(EloquentBlogPost::class);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($post) use ($tags) {
                return $post instanceof BlogPost
                    && $post->tags() === $tags;
            }))
            ->andReturn($eloquentPost);

        $command = new CreateBlogPost($title, $content, $authorId, null, $tags);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentBlogPost::class, $result);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/CreateBlogPostHandler expects/');

        $wrongCommand = new UpdateBlogPost('post-id', 'Title', 'Content');
        ($this->handler)($wrongCommand);
    }

    public function test_creates_draft_post_by_default(): void
    {
        $title = 'Test Blog Post';
        $content = 'This is test content';
        $authorId = Id::generate()->toString();

        $eloquentPost = Mockery::mock(EloquentBlogPost::class);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($post) {
                return $post instanceof BlogPost
                    && $post->status()->equals(PostStatus::draft())
                    && $post->publishedAt() === null;
            }))
            ->andReturn($eloquentPost);

        $command = new CreateBlogPost($title, $content, $authorId);
        $result = ($this->handler)($command);

        $this->assertInstanceOf(EloquentBlogPost::class, $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(BlogPostRepository::class);
        $this->handler = new CreateBlogPostHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

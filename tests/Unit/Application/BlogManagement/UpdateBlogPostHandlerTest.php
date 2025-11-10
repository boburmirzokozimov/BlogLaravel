<?php

namespace Tests\Unit\Application\BlogManagement;

use App\Application\BlogManagement\Commands\CreateBlogPost;
use App\Application\BlogManagement\Commands\UpdateBlogPost;
use App\Application\BlogManagement\Handlers\UpdateBlogPostHandler;
use App\Domain\Blog\Entity\BlogPost;
use App\Domain\Blog\Repositories\BlogPostRepository;
use App\Domain\Blog\ValueObjects\AuthorId;
use App\Domain\Blog\ValueObjects\Content;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class UpdateBlogPostHandlerTest extends UnitTestCase
{
    private BlogPostRepository $repository;

    private UpdateBlogPostHandler $handler;

    public function test_can_update_blog_post(): void
    {
        $postId = Id::generate();
        $post = BlogPost::create(
            Title::new('Original Title'),
            Content::fromString('Original content'),
            AuthorId::fromString(Id::generate()->toString())
        );

        $newTitle = 'Updated Title';
        $newContent = 'Updated content';

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->equals($postId)))
            ->andReturn($post);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($post);

        $command = new UpdateBlogPost($postId->toString(), $newTitle, $newContent);
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_can_update_blog_post_with_custom_slug(): void
    {
        $postId = Id::generate();
        $post = BlogPost::create(
            Title::new('Original Title'),
            Content::fromString('Original content'),
            AuthorId::fromString(Id::generate()->toString())
        );

        $newSlug = 'custom-updated-slug';

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($post);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($savedPost) use ($newSlug) {
                return $savedPost instanceof BlogPost
                    && $savedPost->slug()->value() === $newSlug;
            }));

        $command = new UpdateBlogPost(
            $postId->toString(),
            'Updated Title',
            'Updated content',
            $newSlug
        );
        $result = ($this->handler)($command);

        $this->assertNull($result);
    }

    public function test_can_update_blog_post_tags(): void
    {
        $postId = Id::generate();
        $post = BlogPost::create(
            Title::new('Original Title'),
            Content::fromString('Original content'),
            AuthorId::fromString(Id::generate()->toString())
        );

        $newTags = ['updated', 'tags'];

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($post);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($savedPost) use ($newTags) {
                return $savedPost instanceof BlogPost
                    && $savedPost->tags() === $newTags;
            }));

        $command = new UpdateBlogPost(
            $postId->toString(),
            'Updated Title',
            'Updated content',
            null,
            $newTags
        );
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

        $this->expectException(NotFound::class);

        $command = new UpdateBlogPost($postId->toString(), 'Title', 'Content');
        ($this->handler)($command);
    }

    public function test_throws_exception_when_wrong_command_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/UpdateBlogPostHandler expects/');

        $wrongCommand = new CreateBlogPost('Title', 'Content', Id::generate()->toString());
        ($this->handler)($wrongCommand);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(BlogPostRepository::class);
        $this->handler = new UpdateBlogPostHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

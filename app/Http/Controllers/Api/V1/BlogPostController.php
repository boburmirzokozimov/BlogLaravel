<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\BlogManagement\Commands\ArchiveBlogPost;
use App\Application\BlogManagement\Commands\CreateBlogPost;
use App\Application\BlogManagement\Commands\DeleteBlogPost;
use App\Application\BlogManagement\Commands\PublishBlogPost;
use App\Application\BlogManagement\Commands\UpdateBlogPost;
use App\Application\BlogManagement\Queries\GetBlogPostById;
use App\Application\BlogManagement\Queries\GetBlogPostBySlug;
use App\Application\BlogManagement\Queries\ListPublishedBlogPosts;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\BlogPostCollection;
use App\Http\Resources\BlogPostResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Blog Posts', description: 'Blog Post Management')]
class BlogPostController extends Controller
{
    #[OA\Get(
        path: '/api/v1/blog-posts',
        summary: 'List all published blog posts',
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Number of posts to return',
                schema: new OA\Schema(type: 'integer', default: 10)
            ),
            new OA\Parameter(
                name: 'offset',
                in: 'query',
                description: 'Offset for pagination',
                schema: new OA\Schema(type: 'integer', default: 0)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of published blog posts',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'posts', type: 'array', items: new OA\Items(ref: '#/components/schemas/BlogPost')),
                        new OA\Property(property: 'count', type: 'integer'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $limit = (int) ($request->query('limit', 10));
        $offset = (int) ($request->query('offset', 0));

        $posts = $this->queries->ask(new ListPublishedBlogPosts($limit, $offset));

        return ApiResponse::resource(new BlogPostCollection($posts));
    }

    #[OA\Post(
        path: '/api/v1/blog-posts',
        summary: 'Create a new blog post',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'My First Blog Post'),
                    new OA\Property(property: 'content', type: 'string', example: 'This is the content of my blog post...'),
                    new OA\Property(property: 'slug', type: 'string', example: 'my-first-blog-post', nullable: true),
                    new OA\Property(
                        property: 'tags',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['laravel', 'php']
                    ),
                ]
            )
        ),
        tags: ['Blog Posts'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Blog post created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'en', type: 'string'),
                                new OA\Property(property: 'ru', type: 'string'),
                            ]
                        ),
                        new OA\Property(property: 'post_id', type: 'string', format: 'uuid'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function store(CreateBlogPostRequest $request)
    {

        $authorId = auth()->id();

        $post = $this->commands->dispatch(
            new CreateBlogPost(
                title: $request->validated('title'),
                content: $request->validated('content'),
                authorId: $authorId,
                slug: $request->validated('slug'),
                tags: $request->validated('tags') ?? []
            )
        );

        return ApiResponse::success(
            'blog_post_created_successfully',
            new BlogPostResource($post),
            201
        );
    }

    #[OA\Get(
        path: '/api/v1/blog-posts/{id}',
        summary: 'Get blog post by ID',
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Blog Post UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post details',
                content: new OA\JsonContent(ref: '#/components/schemas/BlogPost')
            ),
            new OA\Response(response: 404, description: 'Blog post not found'),
        ]
    )]
    public function show(string $id)
    {
        $post = $this->queries->ask(new GetBlogPostById($id));

        return ApiResponse::resource(new BlogPostResource($post));
    }

    #[OA\Get(
        path: '/api/v1/blog-posts/slug/{slug}',
        summary: 'Get blog post by slug',
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'Blog Post slug',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post details',
                content: new OA\JsonContent(ref: '#/components/schemas/BlogPost')
            ),
            new OA\Response(response: 404, description: 'Blog post not found'),
        ]
    )]
    public function showBySlug(string $slug)
    {
        $post = $this->queries->ask(new GetBlogPostBySlug($slug));

        return ApiResponse::resource(new BlogPostResource($post));
    }

    #[OA\Put(
        path: '/api/v1/blog-posts/{id}',
        summary: 'Update blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content'],
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'content', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string', nullable: true),
                    new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string')),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'en', type: 'string'),
                                new OA\Property(property: 'ru', type: 'string'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Blog post not found'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function update(UpdateBlogPostRequest $request, string $id)
    {
        $this->commands->dispatch(
            new UpdateBlogPost(
                postId: $id,
                title: $request->validated('title'),
                content: $request->validated('content'),
                slug: $request->validated('slug') ?? null,
                tags: $request->validated('tags') ?? []
            )
        );

        return ApiResponse::success('blog_post_updated_successfully');
    }

    #[OA\Post(
        path: '/api/v1/blog-posts/{id}/publish',
        summary: 'Publish blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post published successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'en', type: 'string'),
                                new OA\Property(property: 'ru', type: 'string'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Blog post not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function publish(string $id)
    {
        $this->commands->dispatch(new PublishBlogPost($id));

        return ApiResponse::success('blog_post_published_successfully');
    }

    #[OA\Post(
        path: '/api/v1/blog-posts/{id}/archive',
        summary: 'Archive blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post archived successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'en', type: 'string'),
                                new OA\Property(property: 'ru', type: 'string'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Blog post not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function archive(string $id)
    {
        $this->commands->dispatch(new ArchiveBlogPost($id));

        return ApiResponse::success('blog_post_archived_successfully');
    }

    #[OA\Delete(
        path: '/api/v1/blog-posts/{id}',
        summary: 'Delete blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'en', type: 'string'),
                                new OA\Property(property: 'ru', type: 'string'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Blog post not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function destroy(string $id)
    {
        $this->commands->dispatch(new DeleteBlogPost($id));

        return ApiResponse::success('blog_post_deleted_successfully');
    }
}

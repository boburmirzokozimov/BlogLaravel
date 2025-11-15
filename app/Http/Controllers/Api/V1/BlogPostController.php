<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Commands\Blog\ArchiveBlogPost;
use App\Application\Commands\Blog\CreateBlogPost;
use App\Application\Commands\Blog\DeleteBlogPost;
use App\Application\Commands\Blog\PublishBlogPost;
use App\Application\Commands\Blog\UpdateBlogPost;
use App\Application\Queries\Blog\GetBlogPostById;
use App\Application\Queries\Blog\GetBlogPostBySlug;
use App\Application\Queries\Blog\ListPublishedBlogPosts;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\BlogPostResource;
use Illuminate\Http\JsonResponse;
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
                name: 'per_page',
                description: 'Number of posts per page',
                in: 'query',
                schema: new OA\Schema(type: 'integer', default: 10, minimum: 1, maximum: 100)
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: 'author_id',
                description: 'Filter by author UUID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of published blog posts',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Success'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Успех'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/BlogPost')
                        ),
                        new OA\Property(
                            property: 'meta',
                            description: 'Simple pagination metadata',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                            ],
                            type: 'object',
                            nullable: true
                        ),
                        new OA\Property(
                            property: 'links',
                            description: 'Simple pagination links',
                            properties: [
                                new OA\Property(property: 'first', type: 'string', example: 'http://localhost:8080/api/v1/blog-posts?page=1'),
                                new OA\Property(property: 'prev', type: 'string', nullable: true, example: null),
                                new OA\Property(property: 'next', type: 'string', nullable: true, example: 'http://localhost:8080/api/v1/blog-posts?page=2'),
                            ],
                            type: 'object',
                            nullable: true
                        ),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $posts = $this->queries->ask(new ListPublishedBlogPosts($request->all()));

        return ApiResponse::success(
            'success',
            BlogPostResource::collection($posts)
        );
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
                    new OA\Property(property: 'title', type: 'string', minLength: 3, maxLength: 255, example: 'My First Blog Post'),
                    new OA\Property(property: 'content', type: 'string', minLength: 10, maxLength: 50000, example: 'This is the content of my blog post...'),
                    new OA\Property(property: 'slug', type: 'string', example: 'my-first-blog-post', nullable: true),
                    new OA\Property(
                        property: 'tags',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['laravel', 'php'],
                        nullable: true
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
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post created successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге успешно создана'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Validation failed'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Ошибка валидации'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'object', example: ['title' => ['The title field is required.']]),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Не аутентифицирован'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(CreateBlogPostRequest $request): JsonResponse
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
            'messages.blog_post_created_successfully',
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
                description: 'Blog Post UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Success'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Успех'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog post not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге не найдена'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function show(string $id)
    {
        $post = $this->queries->ask(new GetBlogPostById($id));

        return ApiResponse::success(
            'success',
            new BlogPostResource($post)
        );
    }

    #[OA\Get(
        path: '/api/v1/blog-posts/slug/{slug}',
        summary: 'Get blog post by slug',
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Blog Post slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'my-first-blog-post')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Success'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Успех'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog post not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге не найдена'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function showBySlug(string $slug): JsonResponse
    {
        $post = $this->queries->ask(new GetBlogPostBySlug($slug));

        return ApiResponse::success(
            'success',
            new BlogPostResource($post)
        );
    }

    #[OA\Put(
        path: '/api/v1/blog-posts/{id}',
        summary: 'Update blog post',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', minLength: 3, maxLength: 255, example: 'Updated Blog Post Title'),
                    new OA\Property(property: 'content', type: 'string', minLength: 10, maxLength: 50000, example: 'Updated content...'),
                    new OA\Property(property: 'slug', type: 'string', example: 'updated-blog-post-title', nullable: true),
                    new OA\Property(
                        property: 'tags',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['laravel', 'php', 'update'],
                        nullable: true
                    ),
                ]
            )
        ),
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Blog Post UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog post updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post updated successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге успешно обновлена'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog post not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге не найдена'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Validation failed'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Ошибка валидации'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'object', example: ['title' => ['The title field is required.']]),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Не аутентифицирован'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - User is not authorized to update this blog post',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Forbidden'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Доступ запрещен'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(UpdateBlogPostRequest $request, string $id)
    {
        $post = $this->commands->dispatch(
            new UpdateBlogPost(
                postId: $id,
                title: $request->validated('title'),
                content: $request->validated('content'),
                slug: $request->validated('slug'),
                tags: $request->validated('tags') ?? []
            )
        );

        return ApiResponse::success(
            'blog_post_updated_successfully',
            new BlogPostResource($post)
        );
    }

    #[OA\Post(
        path: '/api/v1/blog-posts/{id}/publish',
        summary: 'Publish blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Blog Post UUID',
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
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post published successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге успешно опубликована'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog post not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге не найдена'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Не аутентифицирован'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - User is not authorized to publish this blog post',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Forbidden'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Доступ запрещен'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function publish(string $id): JsonResponse
    {
        $post = $this->commands->dispatch(new PublishBlogPost($id));

        return ApiResponse::success(
            'blog_post_published_successfully',
            new BlogPostResource($post)
        );
    }

    #[OA\Post(
        path: '/api/v1/blog-posts/{id}/archive',
        summary: 'Archive blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Blog Post UUID',
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
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post archived successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге успешно архивирована'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog post not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге не найдена'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Не аутентифицирован'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - User is not authorized to archive this blog post',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Forbidden'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Доступ запрещен'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function archive(string $id): JsonResponse
    {
        $post = $this->commands->dispatch(new ArchiveBlogPost($id));

        return ApiResponse::success(
            'blog_post_archived_successfully',
            new BlogPostResource($post)
        );
    }

    #[OA\Delete(
        path: '/api/v1/blog-posts/{id}',
        summary: 'Delete blog post',
        security: [['bearerAuth' => []]],
        tags: ['Blog Posts'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Blog Post UUID',
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
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post deleted successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге успешно удалена'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/BlogPost'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog post not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Blog post not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Статья в блоге не найдена'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Unauthenticated'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Не аутентифицирован'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - User is not authorized to delete this blog post',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Forbidden'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Доступ запрещен'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $post = $this->commands->dispatch(new DeleteBlogPost($id));

        return ApiResponse::success(
            'blog_post_deleted_successfully',
            new BlogPostResource($post)
        );
    }
}

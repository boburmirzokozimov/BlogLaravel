<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Commands\Tag\CreateTag;
use App\Application\Commands\Tag\DeleteTag;
use App\Application\Commands\Tag\UpdateTag;
use App\Application\Queries\Tag\GetTagById;
use App\Application\Queries\Tag\GetTagBySlug;
use App\Application\Queries\Tag\ListTags;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tags', description: 'Tag Management')]
class TagController extends Controller
{
    #[OA\Get(
        path: '/api/v1/tags',
        summary: 'List all tags',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'per_page',
                description: 'Number of tags per page',
                in: 'query',
                schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search tags by name or slug',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'php')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of tags',
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
                            items: new OA\Items(ref: '#/components/schemas/Tag')
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
                                new OA\Property(property: 'first', type: 'string', example: 'http://localhost:8080/api/v1/tags?page=1'),
                                new OA\Property(property: 'prev', type: 'string', example: null, nullable: true),
                                new OA\Property(property: 'next', type: 'string', example: 'http://localhost:8080/api/v1/tags?page=2', nullable: true),
                            ],
                            type: 'object',
                            nullable: true
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->queries->ask(new ListTags($request->all()));

        return ApiResponse::success(
            'success',
            TagResource::collection($paginator->items())
        );
    }

    #[OA\Get(
        path: '/api/v1/tags/{id}',
        summary: 'Get tag by ID',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Tag UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tag details',
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
                        new OA\Property(property: 'data', ref: '#/components/schemas/Tag'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег не найден'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $tag = $this->queries->ask(new GetTagById($id));

        return ApiResponse::success(
            'success',
            new TagResource($tag)
        );
    }

    #[OA\Get(
        path: '/api/v1/tags/slug/{slug}',
        summary: 'Get tag by slug',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Tag slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'php')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tag details',
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
                        new OA\Property(property: 'data', ref: '#/components/schemas/Tag'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег не найден'),
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
        $tag = $this->queries->ask(new GetTagBySlug($slug));

        return ApiResponse::success(
            'success',
            new TagResource($tag)
        );
    }

    #[OA\Post(
        path: '/api/v1/tags',
        summary: 'Create a new tag',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', minLength: 3, maxLength: 255, example: 'PHP'),
                    new OA\Property(property: 'slug', type: 'string', example: 'php', nullable: true),
                ]
            )
        ),
        tags: ['Tags'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tag created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag created successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег успешно создан'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Tag'),
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
                        new OA\Property(property: 'data', type: 'object', example: ['name' => ['The name field is required.']]),
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
    public function store(CreateTagRequest $request): JsonResponse
    {
        $tag = $this->commands->dispatch(
            new CreateTag(
                name: $request->validated('name'),
                slug: $request->validated('slug')
            )
        );

        return ApiResponse::success(
            'messages.tag_created_successfully',
            new TagResource($tag),
            201
        );
    }

    #[OA\Put(
        path: '/api/v1/tags/{id}',
        summary: 'Update tag',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', minLength: 3, maxLength: 255, example: 'Updated Tag Name'),
                    new OA\Property(property: 'slug', type: 'string', example: 'updated-tag-slug', nullable: true),
                ]
            )
        ),
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Tag UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tag updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag updated successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег успешно обновлен'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'null', nullable: true),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег не найден'),
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
                        new OA\Property(property: 'data', type: 'object', example: ['name' => ['The name field is required.']]),
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
    public function update(UpdateTagRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        $this->commands->dispatch(
            new UpdateTag(
                tagId: $id,
                name: $data['name'],
                slug: $data['slug'] ?? null
            )
        );

        return ApiResponse::success(
            'messages.tag_updated_successfully',
            null
        );
    }

    #[OA\Delete(
        path: '/api/v1/tags/{id}',
        summary: 'Delete tag',
        security: [['bearerAuth' => []]],
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Tag UUID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tag deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag deleted successfully'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег успешно удален'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'data', type: 'null', nullable: true),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            properties: [
                                new OA\Property(property: 'en', type: 'string', example: 'Tag not found'),
                                new OA\Property(property: 'ru', type: 'string', example: 'Тег не найден'),
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
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $this->commands->dispatch(new DeleteTag($id));

        return ApiResponse::success(
            'messages.tag_deleted_successfully',
            null
        );
    }
}

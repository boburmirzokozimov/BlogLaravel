<?php

namespace Tests\Unit\Application\TagManagement;

use App\Application\TagManagement\Queries\GetTagById;
use App\Application\TagManagement\Queries\ListTags;
use App\Application\TagManagement\Handlers\ListTagsHandler;
use App\Domain\Blog\Repositories\TagRepository;
use App\Infrastructure\Blog\EloquentTag;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Mockery;
use Tests\UnitTestCase;

class ListTagsHandlerTest extends UnitTestCase
{
    private TagRepository $repository;

    private ListTagsHandler $handler;

    public function test_can_list_tags_without_filters(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('index')
            ->once()
            ->with([])
            ->andReturn($paginator);

        $query = new ListTags();
        $result = ($this->handler)($query);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_can_list_tags_with_filters(): void
    {
        $filters = [
            'search' => 'php',
            'per_page' => 20,
        ];
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('index')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $query = new ListTags($filters);
        $result = ($this->handler)($query);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_passes_search_filter_to_repository(): void
    {
        $filters = ['search' => 'laravel'];
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('index')
            ->once()
            ->with(Mockery::on(function ($passedFilters) use ($filters) {
                return isset($passedFilters['search'])
                    && $passedFilters['search'] === $filters['search'];
            }))
            ->andReturn($paginator);

        $query = new ListTags($filters);
        ($this->handler)($query);
    }

    public function test_passes_per_page_filter_to_repository(): void
    {
        $filters = ['per_page' => 15];
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('index')
            ->once()
            ->with(Mockery::on(function ($passedFilters) use ($filters) {
                return isset($passedFilters['per_page'])
                    && $passedFilters['per_page'] === $filters['per_page'];
            }))
            ->andReturn($paginator);

        $query = new ListTags($filters);
        ($this->handler)($query);
    }

    public function test_throws_exception_when_wrong_query_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ListTagsHandler expects/');

        $wrongQuery = new GetTagById('tag-id');
        ($this->handler)($wrongQuery);
    }

    public function test_returns_paginator_from_repository(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('index')
            ->once()
            ->andReturn($paginator);

        $query = new ListTags();
        $result = ($this->handler)($query);

        $this->assertSame($paginator, $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(TagRepository::class);
        $this->handler = new ListTagsHandler($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}


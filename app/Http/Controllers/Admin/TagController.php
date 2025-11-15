<?php

namespace App\Http\Controllers\Admin;

use App\Application\Commands\Tag\CreateTag;
use App\Application\Commands\Tag\DeleteTag;
use App\Application\Commands\Tag\UpdateTag;
use App\Application\Queries\Tag\GetTagById;
use App\Application\Queries\Tag\ListTags;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Http\Resources\TagResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(Request $request): Response
    {
        $tags = $this->queries->ask(new ListTags($request->all()));

        return Inertia::render('Admin/Tags/Index', ['data' => TagResource::collection($tags)]);
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Tags/Create');
    }

    /**
     * Store a newly created tag.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        $this->commands->dispatch(
            new CreateTag(
                name: $request->validated('name'),
                slug: $request->validated('slug')
            )
        );

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified tag.
     */
    public function show(string $id): Response
    {
        $tag = $this->queries->ask(new GetTagById($id));

        return Inertia::render('Admin/Tags/Show', ['data' => new TagResource($tag)]);
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(string $id): Response
    {
        $tag = $this->queries->ask(new GetTagById($id));

        return Inertia::render('Admin/Tags/Edit', [
            'tag' => new TagResource($tag),
        ]);
    }

    /**
     * Update the specified tag.
     */
    public function update(UpdateTagRequest $request, string $id): RedirectResponse
    {
        $this->commands->dispatch(
            new UpdateTag(
                tagId: $id,
                name: $request->validated('name'),
                slug: $request->validated('slug')
            )
        );

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->commands->dispatch(new DeleteTag($id));

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
}

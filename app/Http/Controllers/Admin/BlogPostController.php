<?php

namespace App\Http\Controllers\Admin;

use App\Application\Commands\Blog\ArchiveBlogPost;
use App\Application\Commands\Blog\PublishBlogPost;
use App\Application\Commands\Blog\UnpublishBlogPost;
use App\Application\Queries\Blog\GetBlogPostById;
use App\Application\Queries\Blog\ListBlogPosts;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBlogPostStatusRequest;
use App\Http\Resources\BlogPostResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request): Response
    {
        $posts = $this->queries->ask(new ListBlogPosts($request->all()));

        return Inertia::render('Admin/BlogPosts/Index', ['data' => BlogPostResource::collection($posts)]);
    }

    /**
     * Display the specified blog post.
     */
    public function show(string $id): Response
    {
        $post = $this->queries->ask(new GetBlogPostById($id));

        return Inertia::render('Admin/BlogPosts/Show', ['data' => new BlogPostResource($post)]);
    }

    /**
     * Show the form for editing the specified blog post status.
     */
    public function edit(string $id): Response
    {
        $post = $this->queries->ask(new GetBlogPostById($id));

        return Inertia::render('Admin/BlogPosts/Edit', [
            'post' => new BlogPostResource($post),
        ]);
    }

    /**
     * Update the specified blog post status.
     */
    public function update(UpdateBlogPostStatusRequest $request, string $id): RedirectResponse
    {
        $status = $request->validated('status');

        // Use appropriate command based on status
        match ($status) {
            'published' => $this->commands->dispatch(new PublishBlogPost($id)),
            'archived' => $this->commands->dispatch(new ArchiveBlogPost($id)),
            'draft' => $this->commands->dispatch(new UnpublishBlogPost($id)),
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post status updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with(['category', 'user'])
            ->latest()
            ->paginate(6);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = Post::create($this->postData($request) + [
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post published successfully.');
    }

    public function show(Post $post): View
    {
        $post->load(['category', 'user']);

        $post->load(['comments' => function ($query) {
            $query->whereNull('parent_id')
                ->with(['user', 'replies.user', 'replies.replies.user'])
                ->latest();
        }]);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('posts.edit', [
            'post' => $post,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->update($this->postData($request));

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Post deleted successfully.');
    }

    /**
     * Normalize validated form input before persistence.
     *
     * @return array<string, mixed>
     */
    private function postData(PostRequest $request): array
    {
        $validated = $request->validated();

        $validated['excerpt'] = $validated['excerpt'] ?: Str::limit($validated['body'], 180);

        return $validated;
    }

    private function categoryOptions()
    {
        return Category::query()->orderBy('name')->get(['id', 'name', 'slug']);
    }
}

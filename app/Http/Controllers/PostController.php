<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->latest()
            ->paginate(6);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = Post::create($this->postData($request));

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post published successfully.');
    }

    public function show(Post $post): View
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        return view('posts.edit', compact('post'));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update($this->postData($request));

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
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
}

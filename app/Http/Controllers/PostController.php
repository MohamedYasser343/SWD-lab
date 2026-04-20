<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ViewRecorder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with(['category', 'user', 'tags'])
            ->published()
            ->latest('published_at')
            ->latest('id')
            ->paginate(6);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create', [
            'categories' => $this->categoryOptions(),
            'statuses' => PostStatus::cases(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = Post::create($this->postData($request) + [
            'user_id' => Auth::id(),
        ]);

        $post->tags()->sync($request->tagIds());

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post saved successfully.');
    }

    public function show(Post $post, Request $request, ViewRecorder $views): View
    {
        if (! $this->canSeePost($post)) {
            abort(404);
        }

        if ($post->status === PostStatus::Published) {
            $views->record($post, $request);
        }

        $post->load(['category', 'user', 'tags']);

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
            'post' => $post->load('tags'),
            'categories' => $this->categoryOptions(),
            'statuses' => PostStatus::cases(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->update($this->postData($request));
        $post->tags()->sync($request->tagIds());

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
     * @return array<string, mixed>
     */
    private function postData(PostRequest $request): array
    {
        $validated = $request->postAttributes();

        $validated['excerpt'] = $validated['excerpt'] ?: Str::limit($validated['body'], 180);

        return $validated;
    }

    private function categoryOptions()
    {
        return Category::query()->orderBy('name')->get(['id', 'name', 'slug']);
    }

    private function canSeePost(Post $post): bool
    {
        if ($post->status === PostStatus::Published) {
            return true;
        }

        $user = Auth::user();

        return $user !== null && $user->id === $post->user_id;
    }
}

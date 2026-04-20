<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Post $post): RedirectResponse
    {
        $post->comments()->create([
            'user_id'   => $request->user()->id,
            'parent_id' => $request->validated('parent_id'),
            'body'      => $request->validated('body'),
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Comment posted.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $post = $comment->post;
        $comment->delete();

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Comment deleted.');
    }
}

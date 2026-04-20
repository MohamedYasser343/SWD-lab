<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post): RedirectResponse
    {
        $user = $request->user();
        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
            $status = 'Like removed.';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $post->increment('likes_count');
            $status = 'Thanks for the like.';
        }

        return back()->with('status', $status);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function toggle(Request $request, Post $post): RedirectResponse
    {
        $user = $request->user();
        $existing = Bookmark::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if ($existing) {
            $existing->delete();
            $status = 'Bookmark removed.';
        } else {
            Bookmark::create(['user_id' => $user->id, 'post_id' => $post->id]);
            $status = 'Bookmarked.';
        }

        return back()->with('status', $status);
    }
}

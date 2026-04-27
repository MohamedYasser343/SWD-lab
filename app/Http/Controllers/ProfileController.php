<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'posts' => $user->posts()->count(),
            'comments' => $user->comments()->count(),
            'likes' => $user->likes()->where('likeable_type', Post::class)->count(),
            'bookmarks' => $user->bookmarks()->count(),
        ];

        return view('profile.show', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profile.show')->with('status', 'Profile updated.');
    }

    public function likes(Request $request): View
    {
        $posts = Post::query()
            ->whereIn('id', $request->user()
                ->likes()
                ->where('likeable_type', Post::class)
                ->select('likeable_id'))
            ->with(['user', 'category', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('profile.likes', [
            'posts' => $posts,
        ]);
    }

    public function bookmarks(Request $request): View
    {
        $posts = Post::query()
            ->whereIn('id', $request->user()->bookmarks()->select('post_id'))
            ->with(['user', 'category', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('profile.bookmarks', [
            'posts' => $posts,
        ]);
    }
}

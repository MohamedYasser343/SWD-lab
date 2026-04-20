<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Contracts\View\View;

class TagController extends Controller
{
    public function show(Tag $tag): View
    {
        $posts = $tag->posts()
            ->with(['category', 'user', 'tags'])
            ->published()
            ->latest('published_at')
            ->latest('posts.id')
            ->paginate(6);

        return view('tags.show', compact('tag', 'posts'));
    }
}

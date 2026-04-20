<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $posts = Post::query()
            ->with(['user', 'category', 'tags'])
            ->published()
            ->when($query !== '', function ($q) use ($query) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $query) . '%';

                $q->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhereHas('tags', fn ($t) => $t->where('name', 'like', $like));
                });
            })
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('search.index', [
            'query' => $query,
            'posts' => $posts,
        ]);
    }
}

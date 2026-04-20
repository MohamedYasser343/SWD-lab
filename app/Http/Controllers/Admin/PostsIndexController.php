<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $statusFilter = $request->query('status');

        $posts = Post::query()
            ->with(['user', 'category', 'tags'])
            ->when(
                $statusFilter && in_array($statusFilter, array_column(PostStatus::cases(), 'value'), true),
                fn ($q) => $q->where('status', $statusFilter),
            )
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'statuses' => PostStatus::cases(),
            'activeStatus' => $statusFilter,
        ]);
    }
}

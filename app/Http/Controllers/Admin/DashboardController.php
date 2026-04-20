<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $postCounts = [
            'total' => Post::count(),
            'published' => Post::where('status', PostStatus::Published->value)->count(),
            'drafts' => Post::where('status', PostStatus::Draft->value)->count(),
            'scheduled' => Post::where('status', PostStatus::Scheduled->value)->count(),
        ];

        $recentPosts = Post::query()
            ->with(['user', 'category'])
            ->latest('updated_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'postCounts' => $postCounts,
            'userCount' => User::count(),
            'tagCount' => Tag::count(),
            'recentPosts' => $recentPosts,
        ]);
    }
}

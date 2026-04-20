<?php

namespace App\Jobs;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishScheduledPostsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Post::query()
            ->where('status', PostStatus::Scheduled->value)
            ->where('published_at', '<=', now())
            ->update(['status' => PostStatus::Published->value]);
    }
}

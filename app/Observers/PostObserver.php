<?php

namespace App\Observers;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    public function saving(Post $post): void
    {
        $post->reading_minutes = $this->computeReadingMinutes($post->body);

        if ($post->isDirty('status')) {
            $this->reconcileScheduling($post);
        }

        if (blank($post->meta_description)) {
            $post->meta_description = Str::limit(strip_tags((string) ($post->excerpt ?: $post->body)), 160);
        }
    }

    private function computeReadingMinutes(?string $body): int
    {
        $words = str_word_count(strip_tags((string) $body));

        return max(1, (int) ceil($words / 200));
    }

    private function reconcileScheduling(Post $post): void
    {
        $status = $post->status instanceof PostStatus ? $post->status : PostStatus::tryFrom((string) $post->status);

        if ($status === PostStatus::Published && ! $post->published_at) {
            $post->published_at = now();
        }

        if ($status === PostStatus::Scheduled && $post->published_at && $post->published_at->isPast()) {
            $post->status = PostStatus::Published;
        }
    }
}

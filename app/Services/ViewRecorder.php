<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;
use Throwable;

class ViewRecorder
{
    public function record(Post $post, Request $request): void
    {
        $dayKey = now()->format('Ymd');
        $userId = $request->user()?->id;
        $sessionId = $this->sessionIdFor($request, $userId);

        if ($this->alreadyRecorded($post, $userId, $sessionId, $dayKey)) {
            return;
        }

        try {
            PostView::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'session_id' => substr($sessionId, 0, 80),
                'day_key' => $dayKey,
                'ip_hash' => hash('sha256', (string) $request->ip() . config('app.key')),
                'viewed_at' => now(),
            ]);

            $post->increment('views_count');
        } catch (Throwable) {
            // Unique constraint violation — already counted today. Swallow silently.
        }
    }

    private function sessionIdFor(Request $request, ?int $userId): string
    {
        if ($userId !== null) {
            return 'user:' . $userId;
        }

        if ($request->hasSession()) {
            return $request->session()->getId();
        }

        return 'ip:' . hash('sha256', (string) $request->ip());
    }

    private function alreadyRecorded(Post $post, ?int $userId, string $sessionId, string $dayKey): bool
    {
        return PostView::query()
            ->where('post_id', $post->id)
            ->where('day_key', $dayKey)
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId !== null) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->exists();
    }
}

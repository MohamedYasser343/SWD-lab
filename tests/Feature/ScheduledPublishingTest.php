<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Jobs\PublishScheduledPostsJob;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScheduledPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_publishes_posts_that_are_due(): void
    {
        $due = Post::factory()->scheduled()->create([
            'published_at' => now()->subMinute(),
        ]);

        $future = Post::factory()->scheduled()->create([
            'published_at' => now()->addDay(),
        ]);

        (new PublishScheduledPostsJob)->handle();

        $this->assertSame(PostStatus::Published, $due->fresh()->status);
        $this->assertSame(PostStatus::Scheduled, $future->fresh()->status);
    }

    public function test_scheduled_posts_are_hidden_from_the_public_index(): void
    {
        Post::factory()->scheduled()->create([
            'title' => 'Future letter',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('posts.index'))->assertDontSeeText('Future letter');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EngagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_and_unlike_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->post(route('posts.like', $post))->assertRedirect();
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
        ]);
        $this->assertSame(1, $post->fresh()->likes_count);

        $this->actingAs($user)->post(route('posts.like', $post))->assertRedirect();
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'likeable_id' => $post->id,
        ]);
        $this->assertSame(0, $post->fresh()->likes_count);
    }

    public function test_guest_cannot_like(): void
    {
        $post = Post::factory()->create();

        $this->post(route('posts.like', $post))->assertRedirect(route('login'));
    }

    public function test_user_can_bookmark_and_unbookmark(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->post(route('posts.bookmark', $post));
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->actingAs($user)->post(route('posts.bookmark', $post));
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_view_count_is_recorded_on_show(): void
    {
        $post = Post::factory()->create();

        $this->get(route('posts.show', $post))->assertOk();

        $this->assertSame(1, $post->fresh()->views_count);
    }

    public function test_view_count_is_deduplicated_for_authed_user_per_day(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->get(route('posts.show', $post));
        $this->actingAs($user)->get(route('posts.show', $post));
        $this->actingAs($user)->get(route('posts.show', $post));

        $this->assertSame(1, $post->fresh()->views_count);
    }
}

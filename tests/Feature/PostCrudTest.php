<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_the_posts_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('posts.index'));
    }

    public function test_posts_index_displays_saved_posts(): void
    {
        $posts = Post::factory()->count(2)->create();

        $response = $this->get(route('posts.index'));

        $response->assertOk();
        $response->assertSeeText($posts[0]->title);
        $response->assertSeeText($posts[1]->title);
    }

    public function test_post_detail_page_uses_the_slug_route_key(): void
    {
        $post = Post::factory()->create([
            'slug' => 'laravel-blog-routing',
        ]);

        $response = $this->get('/posts/laravel-blog-routing');

        $response->assertOk();
        $response->assertSeeText($post->title);
        $response->assertSeeText($post->body);
    }

    public function test_user_can_create_a_post(): void
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'A calm publishing flow for Laravel teams',
            'slug' => '',
            'excerpt' => 'A short teaser for the article card.',
            'body' => 'This post explains how a small Laravel app can offer a complete and pleasant publishing workflow for a team.',
            'status' => 'published',
        ];

        $response = $this->actingAs($user)->post(route('posts.store'), $payload);
        $post = Post::query()->firstOrFail();

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('posts', [
            'title' => $payload['title'],
            'slug' => 'a-calm-publishing-flow-for-laravel-teams',
            'user_id' => $user->id,
        ]);
    }

    public function test_post_creation_is_validated(): void
    {
        $user = User::factory()->create();

        Post::factory()->create([
            'slug' => 'existing-slug',
        ]);

        $response = $this->actingAs($user)
            ->from(route('posts.create'))
            ->post(route('posts.store'), [
                'title' => '',
                'slug' => 'existing-slug',
                'body' => 'Too short',
                'status' => 'published',
            ]);

        $response->assertRedirect(route('posts.create'));
        $response->assertSessionHasErrors(['title', 'slug', 'body']);
    }

    public function test_user_can_update_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => 'Updated newsroom notes',
            'slug' => 'updated-newsroom-notes',
            'excerpt' => 'An updated summary for the refreshed article.',
            'body' => 'The updated article body now covers the improved editing flow and explains the new blog management interface.',
            'status' => 'published',
        ]);

        $post->refresh();

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated newsroom notes',
            'slug' => 'updated-newsroom-notes',
        ]);
    }

    public function test_user_can_delete_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertModelMissing($post);
    }
}

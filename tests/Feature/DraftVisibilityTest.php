<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_drafts_are_hidden_from_the_public_index(): void
    {
        $published = Post::factory()->create(['title' => 'Morning light']);
        $draft = Post::factory()->draft()->create(['title' => 'In progress']);

        $response = $this->get(route('posts.index'));

        $response->assertOk();
        $response->assertSeeText($published->title);
        $response->assertDontSeeText($draft->title);
    }

    public function test_drafts_return_404_to_guests(): void
    {
        $draft = Post::factory()->draft()->create();

        $this->get(route('posts.show', $draft))->assertNotFound();
    }

    public function test_drafts_are_visible_to_their_author(): void
    {
        $author = User::factory()->create();
        $draft = Post::factory()->draft()->create(['user_id' => $author->id]);

        $this->actingAs($author)
            ->get(route('posts.show', $draft))
            ->assertOk()
            ->assertSeeText($draft->title);
    }
}

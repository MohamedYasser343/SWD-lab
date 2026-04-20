<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_page_shows_only_their_published_posts(): void
    {
        $amira = User::factory()->create(['username' => 'amira', 'name' => 'Amira K.']);
        $omar = User::factory()->create(['username' => 'omar', 'name' => 'Omar S.']);

        $theirs = Post::factory()->create(['title' => 'Amira published', 'user_id' => $amira->id]);
        $theirDraft = Post::factory()->draft()->create(['title' => 'Amira drafted', 'user_id' => $amira->id]);
        $othersPost = Post::factory()->create(['title' => 'Omar published', 'user_id' => $omar->id]);

        $response = $this->get(route('authors.show', $amira));

        $response->assertOk();
        $response->assertSeeText('Amira K.');
        $response->assertSeeText($theirs->title);
        $response->assertDontSeeText($theirDraft->title);
        $response->assertDontSeeText($othersPost->title);
    }
}

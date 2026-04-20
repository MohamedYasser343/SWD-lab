<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_page_lists_only_tagged_published_posts(): void
    {
        $tag = Tag::factory()->create(['name' => 'Craft', 'slug' => 'craft']);

        $matchingPost = Post::factory()->create(['title' => 'On slow craft']);
        $matchingPost->tags()->attach($tag);

        $untagged = Post::factory()->create(['title' => 'Not related']);

        $draftWithTag = Post::factory()->draft()->create(['title' => 'Drafted craft']);
        $draftWithTag->tags()->attach($tag);

        $response = $this->get(route('tags.show', $tag));

        $response->assertOk();
        $response->assertSeeText($matchingPost->title);
        $response->assertDontSeeText($untagged->title);
        $response->assertDontSeeText($draftWithTag->title);
    }

    public function test_tag_is_auto_created_from_comma_separated_names(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'A post about nothing',
            'slug' => '',
            'body' => 'A piece of writing about nothing in particular that reaches the minimum body length.',
            'status' => 'published',
            'tags' => 'craft, notes, quiet rooms',
        ]);

        $this->assertDatabaseHas('tags', ['slug' => 'craft']);
        $this->assertDatabaseHas('tags', ['slug' => 'notes']);
        $this->assertDatabaseHas('tags', ['slug' => 'quiet-rooms']);

        $post = Post::query()->firstOrFail();
        $this->assertCount(3, $post->tags);
    }
}

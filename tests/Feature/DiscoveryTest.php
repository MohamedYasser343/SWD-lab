<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_post_title(): void
    {
        Post::factory()->create(['title' => 'On writing slowly', 'body' => 'a long meditation on writing']);
        Post::factory()->create(['title' => 'Notes on teal', 'body' => 'about the color teal and nothing else']);

        $response = $this->get(route('search', ['q' => 'writing']));

        $response->assertOk()
            ->assertSeeText('On writing slowly')
            ->assertDontSeeText('Notes on teal');
    }

    public function test_search_excludes_drafts(): void
    {
        Post::factory()->draft()->create(['title' => 'Hidden draft', 'body' => 'something private here writing in private']);

        $this->get(route('search', ['q' => 'writing']))
            ->assertOk()
            ->assertDontSeeText('Hidden draft');
    }

    public function test_rss_feed_is_served_as_xml(): void
    {
        Post::factory()->create(['title' => 'Featured article']);

        $response = $this->get('/feed.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        $response->assertSee('Featured article', escape: false);
    }

    public function test_sitemap_is_served_as_xml_and_excludes_drafts(): void
    {
        Post::factory()->create(['title' => 'Public']);
        Post::factory()->draft()->create(['title' => 'Hidden', 'slug' => 'hidden-draft']);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertDontSee('hidden-draft', escape: false);
    }
}

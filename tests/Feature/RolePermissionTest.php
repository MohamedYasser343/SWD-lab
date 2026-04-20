<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_readers_are_forbidden_from_admin(): void
    {
        $reader = User::factory()->create();

        $this->actingAs($reader)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admins_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Overview');
    }

    public function test_authors_can_access_admin_dashboard(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_posts_page_filters_by_status(): void
    {
        $admin = User::factory()->admin()->create();

        \App\Models\Post::factory()->create(['title' => 'Published story']);
        \App\Models\Post::factory()->draft()->create(['title' => 'Drafted story']);

        $this->actingAs($admin)
            ->get(route('admin.posts.index', ['status' => 'draft']))
            ->assertOk()
            ->assertSeeText('Drafted story')
            ->assertDontSeeText('Published story');
    }
}

<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_creates_pending_subscriber_with_tokens(): void
    {
        $this->post(route('newsletter.store'), ['email' => 'layla@example.com'])
            ->assertRedirect();

        $subscriber = NewsletterSubscriber::where('email', 'layla@example.com')->firstOrFail();

        $this->assertNotNull($subscriber->confirm_token);
        $this->assertNotNull($subscriber->unsubscribe_token);
        $this->assertNull($subscriber->confirmed_at);
    }

    public function test_confirm_token_activates_subscriber(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'omar@example.com',
            'confirm_token' => 'confirm-1234',
            'unsubscribe_token' => 'unsub-1234',
        ]);

        $this->get(route('newsletter.confirm', 'confirm-1234'))
            ->assertOk()
            ->assertSee('confirmed');

        $subscriber->refresh();
        $this->assertNotNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->confirm_token);
    }

    public function test_unsubscribe_tombstones_the_subscriber(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'nadia@example.com',
            'confirmed_at' => now(),
            'unsubscribe_token' => 'unsub-5678',
        ]);

        $this->get(route('newsletter.unsubscribe', 'unsub-5678'))->assertOk();

        $subscriber->refresh();
        $this->assertNotNull($subscriber->unsubscribed_at);
        $this->assertNull($subscriber->confirmed_at);
    }

    public function test_already_confirmed_subscriber_is_idempotent(): void
    {
        NewsletterSubscriber::create([
            'email' => 'youssef@example.com',
            'confirmed_at' => now(),
            'confirm_token' => null,
            'unsubscribe_token' => 'existing-token',
        ]);

        $this->post(route('newsletter.store'), ['email' => 'youssef@example.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame(1, NewsletterSubscriber::where('email', 'youssef@example.com')->count());
    }
}

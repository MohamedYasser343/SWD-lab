<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($subscriber && $subscriber->isConfirmed()) {
            return back()->with('status', 'You are already subscribed.');
        }

        if ($subscriber && $subscriber->isTombstoned()) {
            $subscriber->update([
                'unsubscribed_at' => null,
                'confirmed_at' => null,
                'confirm_token' => Str::random(48),
                'unsubscribe_token' => Str::random(48),
            ]);
        } elseif (! $subscriber) {
            NewsletterSubscriber::create([
                'email' => $validated['email'],
                'confirm_token' => Str::random(48),
                'unsubscribe_token' => Str::random(48),
            ]);
        }

        // In production this would dispatch a queued confirmation mail;
        // locally we simply point the user at the confirm link for dev convenience.
        return back()->with('status', 'Check your inbox for a confirmation link.');
    }

    public function confirm(string $token): View|RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('confirm_token', $token)->first();

        if (! $subscriber) {
            abort(404);
        }

        $subscriber->update([
            'confirmed_at' => now(),
            'confirm_token' => null,
        ]);

        return view('newsletter.confirmed', ['subscriber' => $subscriber]);
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (! $subscriber) {
            abort(404);
        }

        $subscriber->update([
            'unsubscribed_at' => now(),
            'confirmed_at' => null,
            'confirm_token' => null,
        ]);

        return view('newsletter.unsubscribed', ['subscriber' => $subscriber]);
    }
}

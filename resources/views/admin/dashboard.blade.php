@extends('admin.layouts.admin')

@section('title', 'Overview')

@section('content')
    <div class="max-w-6xl">
        <p class="text-xs uppercase tracking-[0.3em]" style="color: var(--ember);">Overview</p>
        <h1 class="serif mt-2 text-4xl font-semibold leading-tight">Good morning.</h1>
        <p class="mt-3 text-base leading-relaxed" style="color: var(--muted-ink);">
            A quiet view of the lagoon today — drafts, scheduled posts, and the recent stream of activity.
        </p>

        <section class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Published', 'value' => $postCounts['published'], 'tint' => 'var(--lagoon-pale)'],
                ['label' => 'Drafts', 'value' => $postCounts['drafts'], 'tint' => 'var(--cream)'],
                ['label' => 'Scheduled', 'value' => $postCounts['scheduled'], 'tint' => 'var(--cream)'],
                ['label' => 'Tags', 'value' => $tagCount, 'tint' => 'var(--lagoon-pale)'],
            ] as $stat)
                <article class="rounded-2xl border px-5 py-6" style="background: var(--paper-raised); border-color: var(--hairline);">
                    <p class="text-xs uppercase tracking-[0.24em]" style="color: var(--muted-ink);">{{ $stat['label'] }}</p>
                    <p class="serif mt-3 text-4xl font-semibold" style="color: var(--lagoon-deep);">{{ $stat['value'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-10 rounded-2xl border" style="background: var(--paper-raised); border-color: var(--hairline);">
            <header class="flex items-end justify-between border-b px-6 py-5" style="border-color: var(--hairline);">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: var(--ember);">Recent</p>
                    <h2 class="serif mt-1 text-2xl font-semibold">Latest posts</h2>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="text-sm font-semibold hover:underline" style="color: var(--lagoon);">View all →</a>
            </header>

            @if ($recentPosts->isEmpty())
                <p class="px-6 py-8 text-sm" style="color: var(--muted-ink);">No posts yet. Go write one.</p>
            @else
                <ul class="divide-y" style="border-color: var(--hairline);">
                    @foreach ($recentPosts as $post)
                        <li class="flex items-center justify-between px-6 py-4">
                            <div class="min-w-0">
                                <a href="{{ route('posts.show', $post) }}" class="serif text-lg font-semibold hover:underline" style="color: var(--ink);">
                                    {{ $post->title }}
                                </a>
                                <p class="mt-1 text-xs" style="color: var(--muted-ink);">
                                    {{ $post->status?->label() }} ·
                                    by {{ $post->user?->name ?? 'unknown' }} ·
                                    updated {{ $post->updated_at->diffForHumans() }}
                                </p>
                            </div>
                            <a href="{{ route('posts.edit', $post) }}" class="shrink-0 rounded-full px-4 py-1.5 text-xs font-semibold" style="background: var(--lagoon-pale); color: var(--lagoon-deep);">
                                Edit
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection

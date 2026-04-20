@extends('admin.layouts.admin')

@section('title', 'Posts')

@section('content')
    <div class="max-w-6xl">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em]" style="color: var(--ember);">Content</p>
                <h1 class="serif mt-2 text-4xl font-semibold leading-tight">Posts</h1>
                <p class="mt-2 text-sm" style="color: var(--muted-ink);">{{ $posts->total() }} total</p>
            </div>
            <a href="{{ route('posts.create') }}" class="rounded-full px-5 py-2.5 text-sm font-semibold text-white" style="background: var(--lagoon);">
                New post
            </a>
        </div>

        <nav class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('admin.posts.index') }}" @class([
                'rounded-full px-4 py-1.5 text-xs font-semibold',
                'text-white' => blank($activeStatus),
            ]) style="{{ blank($activeStatus) ? 'background: var(--lagoon);' : 'background: var(--lagoon-pale); color: var(--lagoon-deep);' }}">
                All
            </a>
            @foreach ($statuses as $status)
                <a href="{{ route('admin.posts.index', ['status' => $status->value]) }}" @class([
                    'rounded-full px-4 py-1.5 text-xs font-semibold',
                    'text-white' => $activeStatus === $status->value,
                ]) style="{{ $activeStatus === $status->value ? 'background: var(--lagoon);' : 'background: var(--lagoon-pale); color: var(--lagoon-deep);' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </nav>

        <section class="mt-6 overflow-hidden rounded-2xl border" style="background: var(--paper-raised); border-color: var(--hairline);">
            @if ($posts->isEmpty())
                <p class="px-6 py-10 text-center text-sm" style="color: var(--muted-ink);">No posts match this filter.</p>
            @else
                <table class="w-full text-sm">
                    <thead style="background: var(--cream); color: var(--muted-ink);">
                        <tr class="text-left text-[11px] uppercase tracking-[0.2em]">
                            <th class="px-6 py-3 font-semibold">Title</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Author</th>
                            <th class="px-4 py-3 font-semibold">Updated</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--hairline);">
                        @foreach ($posts as $post)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('posts.show', $post) }}" class="serif text-base font-semibold hover:underline" style="color: var(--ink);">
                                        {{ $post->title }}
                                    </a>
                                    @if ($post->tags->isNotEmpty())
                                        <p class="mt-1 text-[11px]" style="color: var(--muted-ink);">
                                            @foreach ($post->tags as $tag)
                                                #{{ $tag->name }}@if (! $loop->last), @endif
                                            @endforeach
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-[0.15em]" style="background: var(--lagoon-pale); color: var(--lagoon-deep);">
                                        {{ $post->status?->label() ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4" style="color: var(--muted-ink);">{{ $post->user?->name ?? '—' }}</td>
                                <td class="px-4 py-4" style="color: var(--muted-ink);">{{ $post->updated_at->diffForHumans() }}</td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('posts.edit', $post) }}" class="text-xs font-semibold hover:underline" style="color: var(--lagoon);">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <div class="mt-6">{{ $posts->links() }}</div>
    </div>
@endsection

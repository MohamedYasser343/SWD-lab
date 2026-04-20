@extends('layouts.blog')

@section('title', $query !== '' ? 'Search · ' . $query : 'Search')

@section('content')
    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Search</p>
        <form action="{{ route('search') }}" method="GET" class="flex flex-wrap gap-3">
            <input
                type="text"
                name="q"
                value="{{ $query }}"
                placeholder="Search titles, excerpts, tags, or bodies..."
                class="flex-1 min-w-[280px] rounded-2xl border border-slate-200 bg-white px-5 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
                autofocus
            >
            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
                Search
            </button>
        </form>

        @if ($query !== '')
            <p class="mt-5 text-sm font-medium uppercase tracking-[0.24em] text-slate-500">
                {{ $posts->total() }} {{ Str::plural('result', $posts->total()) }} for "{{ $query }}"
            </p>
        @endif
    </section>

    @if ($query !== '' && $posts->isEmpty())
        <section class="mt-8 rounded-[2rem] border border-dashed border-slate-300 bg-white/70 px-8 py-14 text-center shadow-sm">
            <h2 class="font-display text-3xl font-semibold text-slate-900">Nothing matched.</h2>
            <p class="mt-3 text-slate-600">Try a shorter query or browse posts from the home page.</p>
        </section>
    @endif

    @if ($posts->isNotEmpty())
        <section class="mt-8 space-y-5">
            @foreach ($posts as $post)
                <article class="rounded-2xl border border-white/80 bg-white/80 p-6 shadow-sm">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                        @if ($post->category)
                            <span class="rounded-full bg-orange-100 px-2.5 py-1 text-orange-800">{{ $post->category->name }}</span>
                        @endif
                        <span>{{ optional($post->published_at)->format('M d, Y') }}</span>
                        <span>·</span>
                        <span>{{ $post->reading_time }} min</span>
                    </div>
                    <h2 class="font-display mt-3 text-2xl font-semibold text-slate-900">
                        <a href="{{ route('posts.show', $post) }}" class="hover:text-orange-700">{{ $post->title }}</a>
                    </h2>
                    <p class="mt-2 text-base leading-7 text-slate-600">{{ Str::limit($post->excerpt, 180) }}</p>
                </article>
            @endforeach
        </section>
        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection

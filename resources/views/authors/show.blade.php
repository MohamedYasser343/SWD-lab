@extends('layouts.blog')

@section('title', $author->name)

@section('content')
    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Author</p>
        <h1 class="font-display text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">
            {{ $author->name }}
        </h1>
        <p class="mt-2 text-sm font-medium text-slate-500">@{{ $author->username }}</p>

        @if ($author->bio)
            <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600">{{ $author->bio }}</p>
        @endif

        <div class="mt-5 flex flex-wrap gap-4 text-sm text-slate-500">
            @if ($author->website)
                <a href="{{ $author->website }}" class="font-semibold hover:text-orange-700" rel="noopener" target="_blank">Website →</a>
            @endif
            @if ($author->twitter)
                <a href="https://twitter.com/{{ ltrim($author->twitter, '@') }}" class="font-semibold hover:text-orange-700" rel="noopener" target="_blank">Twitter →</a>
            @endif
        </div>

        <p class="mt-6 text-sm font-medium uppercase tracking-[0.24em] text-slate-500">
            {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
        </p>
    </section>

    @if ($posts->isEmpty())
        <section class="mt-8 rounded-[2rem] border border-dashed border-slate-300 bg-white/70 px-8 py-14 text-center shadow-sm">
            <h2 class="font-display text-3xl font-semibold text-slate-900">No posts yet.</h2>
        </section>
    @else
        <section class="mt-8 grid gap-6 lg:grid-cols-2">
            @foreach ($posts as $post)
                <article class="group flex h-full flex-col rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.7)] transition hover:-translate-y-1">
                    <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                        <span>
                            @if ($post->category)
                                <span class="rounded-full bg-orange-100 px-2.5 py-1 text-orange-800">{{ $post->category->name }}</span>
                            @else
                                Article
                            @endif
                        </span>
                        <time>{{ optional($post->published_at ?? $post->created_at)->format('M d, Y') }}</time>
                    </div>
                    <h2 class="font-display mt-5 text-3xl font-semibold leading-tight text-slate-900">
                        <a class="transition group-hover:text-orange-700" href="{{ route('posts.show', $post) }}">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="mt-4 flex-1 text-base leading-8 text-slate-600">{{ Str::limit($post->excerpt, 160) }}</p>
                    <p class="mt-4 text-xs text-slate-500">{{ $post->reading_time }} min read</p>
                </article>
            @endforeach
        </section>

        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection

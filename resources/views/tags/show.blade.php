@extends('layouts.blog')

@section('title', '#' . $tag->name)

@section('content')
    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Tag</p>
        <h1 class="font-display max-w-3xl text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">
            #{{ $tag->name }}
        </h1>
        @if ($tag->description)
            <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">{{ $tag->description }}</p>
        @endif
        <p class="mt-5 text-sm font-medium uppercase tracking-[0.24em] text-slate-500">
            {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} tagged
        </p>
    </section>

    @if ($posts->isEmpty())
        <section class="mt-8 rounded-[2rem] border border-dashed border-slate-300 bg-white/70 px-8 py-14 text-center shadow-sm">
            <h2 class="font-display text-3xl font-semibold text-slate-900">Nothing here yet.</h2>
            <p class="mt-3 text-slate-600">No published posts carry this tag.</p>
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
                        <time datetime="{{ optional($post->published_at ?? $post->created_at)->toDateString() }}">
                            {{ optional($post->published_at ?? $post->created_at)->format('M d, Y') }}
                        </time>
                    </div>

                    <h2 class="font-display mt-5 text-3xl font-semibold leading-tight text-slate-900">
                        <a class="transition group-hover:text-orange-700" href="{{ route('posts.show', $post) }}">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <p class="mt-4 flex-1 text-base leading-8 text-slate-600">
                        {{ Str::limit($post->excerpt, 160) }}
                    </p>
                </article>
            @endforeach
        </section>

        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection

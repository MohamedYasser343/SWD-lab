@extends('layouts.blog')

@section('title', $post->title . ' · The Lagoon')
@section('meta_description', $post->meta_description_or_default)
@section('og_title', $post->meta_title_or_default)
@section('og_description', $post->meta_description_or_default)
@section('og_type', 'article')
@section('canonical', route('posts.show', $post))
@if ($post->og_image_url)
    @section('og_image', $post->og_image_url)
@endif

@php
    $toc = app(\App\Services\TableOfContentsExtractor::class)->extract($post->body);
@endphp

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-white hover:text-slate-900" href="{{ route('posts.index') }}">
            <span aria-hidden="true">&larr;</span> Back to Posts
        </a>

        @can('update', $post)
            <div class="flex flex-wrap items-center gap-3">
                <a class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-white hover:text-slate-900" href="{{ route('posts.edit', $post) }}">
                    Edit Post
                </a>

                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')

                        <button class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 focus:outline-none focus:ring-4 focus:ring-rose-200" type="submit">
                            Delete Post
                        </button>
                    </form>
                @endcan
            </div>
        @endcan
    </div>

    <article class="mt-6 rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl md:p-12">
        <div class="flex flex-wrap items-center gap-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">
            <span>Published {{ $post->created_at->format('F j, Y') }}</span>
            @if ($post->category)
                <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs text-orange-800">
                    {{ $post->category->name }}
                </span>
            @endif
        </div>

        @if ($post->user)
            <div class="mt-4 flex items-center gap-2 text-sm text-slate-500">
                <span
                    class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white"
                    aria-hidden="true"
                >
                    {{ strtoupper(mb_substr($post->user->name, 0, 1)) }}
                </span>
                <span>
                    By
                    <a href="{{ route('authors.show', $post->user) }}" class="font-semibold text-slate-700 hover:text-orange-700">{{ $post->user->name }}</a>
                </span>
                <span aria-hidden="true">·</span>
                <span>{{ $post->reading_time }} min read</span>
            </div>
        @endif

        <h1 class="font-display mt-4 max-w-4xl text-4xl font-semibold leading-tight text-slate-900 md:text-6xl">
            {{ $post->title }}
        </h1>

        @if ($post->excerpt)
            <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-600">
                {{ $post->excerpt }}
            </p>
        @endif

        @if ($post->tags->isNotEmpty())
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-800 transition hover:bg-orange-200">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 grid gap-10 lg:grid-cols-[1fr_280px]">
            <div class="text-lg leading-9 whitespace-pre-line text-slate-700">
                {{ $post->body }}
            </div>

            @if (! empty($toc))
                <aside class="lg:sticky lg:top-10 lg:self-start">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-700">On this page</p>
                    <ul class="mt-4 space-y-2 border-l border-slate-200 pl-4 text-sm text-slate-600">
                        @foreach ($toc as $node)
                            <li>
                                <a href="#{{ $node['anchor'] }}" class="hover:text-orange-700">{{ $node['text'] }}</a>
                                @if (! empty($node['children']))
                                    <ul class="mt-1 ml-3 space-y-1 border-l border-slate-100 pl-3 text-xs">
                                        @foreach ($node['children'] as $child)
                                            <li><a href="#{{ $child['anchor'] }}" class="hover:text-orange-700">{{ $child['text'] }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </aside>
            @endif
        </div>

        @auth
            <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6">
                <form action="{{ route('posts.like', $post) }}" method="POST">
                    @csrf
                    @php $liked = $post->isLikedBy(auth()->user()); @endphp
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $liked ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-700' }}">
                        <span aria-hidden="true">{{ $liked ? '♥' : '♡' }}</span>
                        <span>{{ $liked ? 'Liked' : 'Like' }}</span>
                        <span class="text-xs opacity-70">{{ $post->likes_count }}</span>
                    </button>
                </form>
                <form action="{{ route('posts.bookmark', $post) }}" method="POST">
                    @csrf
                    @php $bookmarked = $post->isBookmarkedBy(auth()->user()); @endphp
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $bookmarked ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
                        <span aria-hidden="true">{{ $bookmarked ? '★' : '☆' }}</span>
                        <span>{{ $bookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                    </button>
                </form>
                <span class="ml-auto text-xs font-medium uppercase tracking-[0.24em] text-slate-400">
                    {{ $post->views_count }} {{ Str::plural('view', $post->views_count) }}
                </span>
            </div>
        @else
            <div class="mt-10 border-t border-slate-100 pt-4 text-sm text-slate-500">
                <a class="font-semibold text-orange-700 hover:underline" href="{{ route('login') }}">Log in</a> to like, bookmark, or comment.
                <span class="ml-3 text-xs font-medium uppercase tracking-[0.24em] text-slate-400">
                    {{ $post->views_count }} {{ Str::plural('view', $post->views_count) }}
                </span>
            </div>
        @endauth

        @if ($post->created_at->lt($post->updated_at))
            <p class="mt-10 border-t border-slate-100 pt-4 text-xs font-medium uppercase tracking-[0.24em] text-slate-400">
                Last updated {{ $post->updated_at->diffForHumans() }}
            </p>
        @endif
    </article>

    @include('posts._comments', ['comments' => $post->comments, 'post' => $post])
@endsection

@extends('layouts.blog')

@section('title', 'Blog Dashboard')

@section('content')
    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <div class="grid gap-8 lg:grid-cols-[1.4fr_0.9fr] lg:items-end">
            <div>
                <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Editorial Workspace</p>
                <h1 class="font-display max-w-3xl text-4xl font-semibold leading-tight text-slate-900 md:text-6xl">
                    Every story, one calm place to write it.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600 md:text-lg">
                    Browse the latest posts, keep drafts moving, and jump in to publish whenever inspiration strikes.
                </p>

                @auth
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a class="inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300" href="{{ route('posts.create') }}">
                            New Post
                        </a>
                    </div>
                @endauth
            </div>

            <div class="rounded-[1.75rem] border border-orange-100 bg-orange-50/90 p-6">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Overview</p>
                <div class="mt-4 flex items-end gap-4">
                    <span class="font-display text-6xl font-semibold text-slate-900">{{ $posts->total() }}</span>
                    <span class="pb-2 text-sm font-medium uppercase tracking-[0.24em] text-slate-500">
                        {{ Str::plural('Post', $posts->total()) }} published
                    </span>
                </div>
                <p class="mt-4 text-sm leading-7 text-slate-600">
                    @auth
                        Start a new draft or revisit a post to keep polishing it.
                    @else
                        Sign in to write, edit, and join the conversation.
                    @endauth
                </p>
            </div>
        </div>
    </section>

    @if ($posts->isEmpty())
        <section class="mt-8 rounded-[2rem] border border-dashed border-slate-300 bg-white/70 px-8 py-14 text-center shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">No posts yet</p>
            <h2 class="font-display mt-4 text-3xl font-semibold text-slate-900">Your first article starts here.</h2>
            <p class="mx-auto mt-3 max-w-xl text-base leading-7 text-slate-600">
                @auth
                    Create the first post and the dashboard will instantly turn into your publishing hub.
                @else
                    <a class="font-semibold text-orange-700 hover:underline" href="{{ route('login') }}">Sign in</a>
                    or
                    <a class="font-semibold text-orange-700 hover:underline" href="{{ route('register') }}">create an account</a>
                    to start publishing.
                @endauth
            </p>

            @auth
                <a class="mt-8 inline-flex rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300" href="{{ route('posts.create') }}">
                    Create First Post
                </a>
            @endauth
        </section>
    @else
        <section class="mt-8 grid gap-6 lg:grid-cols-2">
            @foreach ($posts as $post)
                <article class="group flex h-full flex-col rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.7)] transition hover:-translate-y-1 hover:shadow-[0_30px_70px_-38px_rgba(194,65,12,0.45)]">
                    <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                        <span>
                            @if ($post->category)
                                <span class="rounded-full bg-orange-100 px-2.5 py-1 text-orange-800">{{ $post->category->name }}</span>
                            @else
                                Article
                            @endif
                        </span>
                        <time datetime="{{ $post->created_at->toDateString() }}">
                            {{ $post->created_at->format('M d, Y') }}
                        </time>
                    </div>

                    <h2 class="font-display mt-5 text-3xl font-semibold leading-tight text-slate-900">
                        <a class="transition group-hover:text-orange-700" href="{{ route('posts.show', $post) }}">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <p class="mt-4 flex-1 text-base leading-8 text-slate-600">
                        {{ \Illuminate\Support\Str::limit($post->excerpt, 160) }}
                    </p>

                    @if ($post->user)
                        <p class="mt-5 text-xs font-medium text-slate-500">
                            By <span class="font-semibold text-slate-700">{{ $post->user->name }}</span>
                        </p>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition group-hover:bg-orange-600 focus:outline-none focus:ring-4 focus:ring-slate-300" href="{{ route('posts.show', $post) }}">
                            Read Post
                        </a>
                        @can('update', $post)
                            <a class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900" href="{{ route('posts.edit', $post) }}">
                                Edit
                            </a>
                        @endcan
                    </div>
                </article>
            @endforeach
        </section>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
@endsection

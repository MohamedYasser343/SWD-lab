@extends('layouts.blog')

@section('title', $post->title)

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
                <span>By <span class="font-semibold text-slate-700">{{ $post->user->name }}</span></span>
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

        <div class="mt-10 text-lg leading-9 whitespace-pre-line text-slate-700">
            {{ $post->body }}
        </div>

        @if ($post->created_at->lt($post->updated_at))
            <p class="mt-10 border-t border-slate-100 pt-4 text-xs font-medium uppercase tracking-[0.24em] text-slate-400">
                Last updated {{ $post->updated_at->diffForHumans() }}
            </p>
        @endif
    </article>

    @include('posts._comments', ['comments' => $post->comments, 'post' => $post])
@endsection

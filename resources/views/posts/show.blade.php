@extends('layouts.blog')

@section('title', $post->title)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a class="inline-flex rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-white hover:text-slate-900" href="{{ route('posts.index') }}">
            Back to Posts
        </a>

        <div class="flex flex-wrap items-center gap-3">
            <a class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-white hover:text-slate-900" href="{{ route('posts.edit', $post) }}">
                Edit Post
            </a>

            <form action="{{ route('posts.destroy', $post) }}" method="POST">
                @csrf
                @method('DELETE')

                <button class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500" type="submit">
                    Delete Post
                </button>
            </form>
        </div>
    </div>

    <article class="mt-6 rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl md:p-12">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">
            Published {{ $post->created_at->format('F j, Y') }}
        </p>

        <h1 class="font-display mt-4 max-w-4xl text-4xl font-semibold leading-tight text-slate-900 md:text-6xl">
            {{ $post->title }}
        </h1>

        <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-600">
            {{ $post->excerpt }}
        </p>

        <div class="mt-10 rounded-[1.5rem] border border-slate-100 bg-slate-50/80 p-6">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Slug</p>
            <p class="text-sm font-medium text-slate-700">{{ $post->slug }}</p>
        </div>

        <div class="mt-10 text-lg leading-9 whitespace-pre-line text-slate-700">
            {{ $post->body }}
        </div>
    </article>
@endsection

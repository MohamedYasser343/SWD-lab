@extends('layouts.blog')

@section('title', 'Create Post')

@section('content')
    <section class="rounded-[2rem] border border-white/70 bg-white/75 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">New Draft</p>
        <h1 class="font-display mt-4 text-4xl font-semibold text-slate-900 md:text-5xl">Write a fresh story.</h1>
        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
            Add a title, optional slug, short excerpt, and the full article body. If you skip the slug, it will be generated from the title.
        </p>
    </section>

    <section class="mt-8 rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <form action="{{ route('posts.store') }}" class="space-y-8" method="POST">
            @csrf

            @include('posts._form', ['submitLabel' => 'Publish Post'])
        </form>
    </section>
@endsection

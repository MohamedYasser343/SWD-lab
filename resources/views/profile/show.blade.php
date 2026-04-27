@extends('layouts.blog')

@section('title', 'Profile')

@section('content')
    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Profile</p>
        <h1 class="font-display text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">
            {{ $user->name }}
        </h1>
        <p class="mt-2 text-sm font-medium text-slate-500">
            @if ($user->username)@{{ $user->username }} ·@endif {{ $user->email }}
        </p>

        @if ($user->bio)
            <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600">{{ $user->bio }}</p>
        @endif

        <div class="mt-5 flex flex-wrap gap-4 text-sm text-slate-500">
            @if ($user->website)
                <a href="{{ $user->website }}" class="font-semibold hover:text-orange-700" rel="noopener" target="_blank">Website →</a>
            @endif
            @if ($user->twitter)
                <a href="https://twitter.com/{{ ltrim($user->twitter, '@') }}" class="font-semibold hover:text-orange-700" rel="noopener" target="_blank">Twitter →</a>
            @endif
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('profile.edit') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700">
                Edit profile
            </a>
            @if ($user->username)
                <a href="{{ route('authors.show', $user) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700">
                    Public author page
                </a>
            @endif
        </div>
    </section>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $tiles = [
                ['label' => 'Posts', 'value' => $stats['posts'], 'href' => $user->username ? route('authors.show', $user) : null],
                ['label' => 'Comments', 'value' => $stats['comments'], 'href' => null],
                ['label' => 'Liked posts', 'value' => $stats['likes'], 'href' => route('profile.likes')],
                ['label' => 'Bookmarks', 'value' => $stats['bookmarks'], 'href' => route('profile.bookmarks')],
            ];
        @endphp
        @foreach ($tiles as $tile)
            @php $tag = $tile['href'] ? 'a' : 'div'; @endphp
            <{{ $tag }} @if ($tile['href']) href="{{ $tile['href'] }}" @endif class="block rounded-[1.5rem] border border-white/70 bg-white/75 p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $tile['label'] }}</p>
                <p class="font-display mt-3 text-3xl font-semibold text-slate-900">{{ $tile['value'] }}</p>
            </{{ $tag }}>
        @endforeach
    </section>
@endsection

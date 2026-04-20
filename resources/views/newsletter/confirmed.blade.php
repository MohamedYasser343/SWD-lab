@extends('layouts.blog')

@section('title', 'Subscription confirmed')

@section('content')
    <section class="rounded-[2rem] border border-emerald-200 bg-emerald-50/90 p-10 text-center shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-700">Newsletter</p>
        <h1 class="font-display mt-3 text-4xl font-semibold text-slate-900">You're in.</h1>
        <p class="mx-auto mt-4 max-w-xl text-base leading-8 text-slate-600">
            {{ $subscriber->email }} is confirmed for the Lagoon letter. Twice a month, nothing more.
        </p>
        <a href="{{ route('posts.index') }}" class="mt-6 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">
            Back to the blog
        </a>
    </section>
@endsection

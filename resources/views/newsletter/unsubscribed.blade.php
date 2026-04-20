@extends('layouts.blog')

@section('title', 'Unsubscribed')

@section('content')
    <section class="rounded-[2rem] border border-slate-200 bg-white/80 p-10 text-center shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Newsletter</p>
        <h1 class="font-display mt-3 text-4xl font-semibold text-slate-900">Done.</h1>
        <p class="mx-auto mt-4 max-w-xl text-base leading-8 text-slate-600">
            {{ $subscriber->email }} has been removed from the list. You will not hear from us again.
        </p>
    </section>
@endsection

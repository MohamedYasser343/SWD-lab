@extends('layouts.blog')

@section('title', 'Sign In — Inkwell')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Welcome Back</p>
            <h1 class="font-display mb-8 text-3xl font-bold text-slate-900">Sign in to Inkwell.</h1>

            <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100 @error('email') border-rose-400 focus:border-rose-400 focus:ring-rose-100 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100 @error('password') border-rose-400 focus:border-rose-400 focus:ring-rose-100 @enderror"
                    >
                    @error('password')
                        <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-300"
                    >
                    <label for="remember" class="text-sm text-slate-600">Remember me</label>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
                >
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-orange-700 hover:underline">Register</a>
            </p>
        </div>
    </div>
@endsection

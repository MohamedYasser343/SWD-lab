@extends('layouts.blog')

@section('title', 'Edit profile')

@section('content')
    <section class="mx-auto max-w-3xl rounded-[2rem] border border-white/70 bg-white/85 p-8 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.55)] backdrop-blur-xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Profile</p>
        <h1 class="font-display text-4xl font-semibold leading-tight text-slate-900">Edit your profile</h1>

        <form action="{{ route('profile.update') }}" method="POST" class="mt-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Name</label>
                <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                @error('name') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="username" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                <p class="mt-1 text-xs text-slate-500">Used in your public author URL: /authors/{username}</p>
                @error('username') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="bio" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Bio</label>
                <textarea id="bio" name="bio" rows="4"
                          class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100">{{ old('bio', $user->bio) }}</textarea>
                @error('bio') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="avatar" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Avatar URL</label>
                    <input id="avatar" name="avatar" type="text" value="{{ old('avatar', $user->avatar) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                    @error('avatar') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="twitter" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Twitter handle</label>
                    <input id="twitter" name="twitter" type="text" value="{{ old('twitter', $user->twitter) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                    @error('twitter') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="website" class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Website</label>
                <input id="website" name="website" type="text" value="{{ old('website', $user->website) }}"
                       class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                @error('website') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <a href="{{ route('profile.show') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Cancel</a>
                <button type="submit" class="rounded-full bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                    Save changes
                </button>
            </div>
        </form>
    </section>
@endsection

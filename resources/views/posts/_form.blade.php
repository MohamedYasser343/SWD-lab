@php
    $post = $post ?? null;
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="title">Title</label>
        <input
            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $post?->title) }}"
            placeholder="A headline readers will remember"
        >
        @error('title')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="slug">Slug</label>
        <input
            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $post?->slug) }}"
            placeholder="leave blank to generate from the title"
        >
        @error('slug')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="excerpt">Excerpt</label>
        <textarea
            class="min-h-28 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
            id="excerpt"
            name="excerpt"
            rows="4"
            placeholder="A short summary for the blog card"
        >{{ old('excerpt', $post?->excerpt) }}</textarea>
        @error('excerpt')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="body">Body</label>
        <textarea
            class="min-h-72 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
            id="body"
            name="body"
            rows="14"
            placeholder="Write your full article here..."
        >{{ old('body', $post?->body) }}</textarea>
        @error('body')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex flex-wrap items-center gap-3">
    <button class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700" type="submit">
        {{ $submitLabel }}
    </button>

    <a class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900" href="{{ $post ? route('posts.show', $post) : route('posts.index') }}">
        Cancel
    </a>
</div>

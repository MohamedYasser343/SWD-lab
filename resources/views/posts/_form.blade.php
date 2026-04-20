@php
    $post = $post ?? null;
    $categories = $categories ?? collect();
    $currentCategory = old('category', $post?->category?->name);

    $baseField = 'w-full rounded-2xl border bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:ring-4';
    $okField = 'border-slate-200 focus:border-orange-300 focus:ring-orange-100';
    $errField = 'border-rose-400 focus:border-rose-400 focus:ring-rose-100';

    $fieldClass = fn (string $name, string $base = null) =>
        trim(($base ?? $baseField) . ' ' . ($errors->has($name) ? $errField : $okField));
@endphp

@if ($errors->any())
    <div
        class="rounded-2xl border border-rose-200 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm"
        role="alert"
        aria-live="polite"
    >
        <p class="font-semibold">We couldn't save this post yet.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="title">Title</label>
        <input
            class="{{ $fieldClass('title') }}"
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $post?->title) }}"
            placeholder="A headline readers will remember"
            required
            maxlength="255"
            autocomplete="off"
            @error('title') aria-invalid="true" aria-describedby="title-error" @enderror
        >
        @error('title')
            <p id="title-error" class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="slug">
            Slug <span class="ml-1 text-xs font-normal text-slate-400">optional</span>
        </label>
        <input
            class="{{ $fieldClass('slug') }}"
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $post?->slug) }}"
            placeholder="auto-generated from the title"
            maxlength="255"
            autocomplete="off"
            aria-describedby="slug-hint @error('slug') slug-error @enderror"
            @error('slug') aria-invalid="true" @enderror
        >
        <p id="slug-hint" class="mt-1.5 text-xs text-slate-500">Lowercase, hyphenated. Appears in the URL.</p>
        @error('slug')
            <p id="slug-error" class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="category">
            Category <span class="ml-1 text-xs font-normal text-slate-400">optional</span>
        </label>
        <input
            class="{{ $fieldClass('category_id') }}"
            id="category"
            name="category"
            type="text"
            value="{{ $currentCategory }}"
            placeholder="Pick one or type a new name"
            list="category-options"
            maxlength="255"
            autocomplete="off"
            aria-describedby="category-hint @error('category_id') category-error @enderror"
            @error('category_id') aria-invalid="true" @enderror
        >
        @if ($categories->isNotEmpty())
            <datalist id="category-options">
                @foreach ($categories as $category)
                    <option value="{{ $category->name }}"></option>
                @endforeach
            </datalist>
        @endif
        <p id="category-hint" class="mt-1.5 text-xs text-slate-500">Reuse an existing topic or add a fresh one.</p>
        @error('category_id')
            <p id="category-error" class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="excerpt">
            Excerpt <span class="ml-1 text-xs font-normal text-slate-400">optional</span>
        </label>
        <textarea
            class="min-h-28 {{ $fieldClass('excerpt') }}"
            id="excerpt"
            name="excerpt"
            rows="4"
            maxlength="500"
            placeholder="A short summary for the blog card"
            aria-describedby="excerpt-hint @error('excerpt') excerpt-error @enderror"
            @error('excerpt') aria-invalid="true" @enderror
        >{{ old('excerpt', $post?->excerpt) }}</textarea>
        <p id="excerpt-hint" class="mt-1.5 text-xs text-slate-500">Up to 500 characters. Leave blank and we'll use the opening of the body.</p>
        @error('excerpt')
            <p id="excerpt-error" class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-slate-700" for="body">Body</label>
        <textarea
            class="min-h-72 {{ $fieldClass('body') }}"
            id="body"
            name="body"
            rows="14"
            placeholder="Write your full article here..."
            required
            aria-describedby="body-hint @error('body') body-error @enderror"
            @error('body') aria-invalid="true" @enderror
        >{{ old('body', $post?->body) }}</textarea>
        <p id="body-hint" class="mt-1.5 text-xs text-slate-500">Minimum 20 characters. Line breaks are preserved.</p>
        @error('body')
            <p id="body-error" class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex flex-wrap items-center gap-3">
    <button class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300" type="submit">
        {{ $submitLabel }}
    </button>

    <a class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200" href="{{ $post ? route('posts.show', $post) : route('posts.index') }}">
        Cancel
    </a>
</div>

@php
    $commentCount = $comments->count();
    $commentFieldClass = 'w-full rounded-2xl border bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:ring-4 '
        . ($errors->has('body')
            ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-100'
            : 'border-slate-200 focus:border-orange-300 focus:ring-orange-100');
@endphp

<section class="mt-10" aria-labelledby="comments-heading">
    <h2 id="comments-heading" class="font-display text-2xl font-semibold text-slate-900">
        {{ $commentCount }} {{ \Illuminate\Support\Str::plural('Comment', $commentCount) }}
    </h2>

    @auth
        <div class="mt-6 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur-xl">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-orange-700">Leave a Comment</p>
            <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="parent_id" value="">

                <label for="comment-body" class="sr-only">Your comment</label>
                <textarea
                    id="comment-body"
                    name="body"
                    rows="4"
                    required
                    minlength="3"
                    maxlength="2000"
                    placeholder="Share your thoughts..."
                    aria-describedby="@error('body') comment-body-error @enderror"
                    @error('body') aria-invalid="true" @enderror
                    class="{{ $commentFieldClass }}"
                >{{ old('body') }}</textarea>
                @error('body')
                    <p id="comment-body-error" class="text-sm font-medium text-rose-600">{{ $message }}</p>
                @enderror
                <button
                    type="submit"
                    class="rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300"
                >
                    Post Comment
                </button>
            </form>
        </div>
    @else
        <div class="mt-6 rounded-[2rem] border border-dashed border-slate-200 bg-white/60 p-6 text-sm text-slate-600">
            <a class="font-semibold text-orange-700 hover:underline" href="{{ route('login') }}">Sign in</a>
            or
            <a class="font-semibold text-orange-700 hover:underline" href="{{ route('register') }}">create an account</a>
            to join the conversation.
        </div>
    @endauth

    @if ($commentCount === 0)
        <p class="mt-8 text-sm text-slate-500">No comments yet — be the first to share your thoughts.</p>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($comments as $comment)
                @include('posts._comment_thread', ['comment' => $comment, 'depth' => 0, 'post' => $post])
            @endforeach
        </div>
    @endif
</section>

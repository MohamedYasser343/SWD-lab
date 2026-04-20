@php $maxDepth = 3; @endphp

<div class="{{ $depth > 0 ? 'ml-8 border-l-2 border-orange-100 pl-6' : '' }} mt-4">
    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur-xl">

        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-xs font-bold text-orange-700"
                    aria-hidden="true"
                >
                    {{ strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                </span>
                <span class="text-sm font-semibold text-slate-800">{{ $comment->user->name }}</span>
                <time
                    class="text-xs text-slate-400"
                    datetime="{{ $comment->created_at->toIso8601String() }}"
                    title="{{ $comment->created_at->format('F j, Y g:i a') }}"
                >
                    {{ $comment->created_at->diffForHumans() }}
                </time>
            </div>

            @auth
                @can('delete', $comment)
                    <form
                        action="{{ route('comments.destroy', $comment) }}"
                        method="POST"
                        onsubmit="return confirm('Delete this comment?');"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="text-xs font-medium text-rose-500 transition hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-200 rounded"
                            aria-label="Delete this comment"
                        >
                            Delete
                        </button>
                    </form>
                @endcan
            @endauth
        </div>

        <p class="mt-3 text-sm leading-7 whitespace-pre-line text-slate-700">{{ $comment->body }}</p>

        @auth
            @if ($depth < $maxDepth)
                <details class="mt-3 group">
                    <summary class="inline-flex cursor-pointer list-none items-center gap-1 text-xs font-semibold text-orange-700 hover:underline focus:outline-none focus:ring-2 focus:ring-orange-200 rounded">
                        <span class="transition group-open:hidden">Reply</span>
                        <span class="hidden transition group-open:inline">Cancel reply</span>
                    </summary>
                    <div class="mt-3">
                        <form action="{{ route('comments.store', $post) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <label for="reply-body-{{ $comment->id }}" class="sr-only">Reply to {{ $comment->user->name }}</label>
                            <textarea
                                id="reply-body-{{ $comment->id }}"
                                name="body"
                                rows="3"
                                required
                                minlength="3"
                                maxlength="2000"
                                placeholder="Write a reply..."
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
                            ></textarea>
                            <button
                                type="submit"
                                class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300"
                            >
                                Post Reply
                            </button>
                        </form>
                    </div>
                </details>
            @endif
        @endauth
    </div>

    @if ($comment->replies->isNotEmpty() && $depth < $maxDepth)
        @foreach ($comment->replies as $reply)
            @include('posts._comment_thread', [
                'comment' => $reply,
                'depth'   => $depth + 1,
                'post'    => $post,
            ])
        @endforeach
    @endif
</div>

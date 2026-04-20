<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'The Lagoon')</title>

        @hasSection('meta_description')
            <meta name="description" content="@yield('meta_description')">
        @endif

        @hasSection('og_title')
            <meta property="og:title" content="@yield('og_title')">
            <meta property="twitter:title" content="@yield('og_title')">
        @endif

        @hasSection('og_description')
            <meta property="og:description" content="@yield('og_description')">
            <meta property="twitter:description" content="@yield('og_description')">
        @endif

        @hasSection('og_image')
            <meta property="og:image" content="@yield('og_image')">
            <meta property="twitter:image" content="@yield('og_image')">
            <meta property="twitter:card" content="summary_large_image">
        @endif

        @hasSection('canonical')
            <link rel="canonical" href="@yield('canonical')">
            <meta property="og:url" content="@yield('canonical')">
        @endif

        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:site_name" content="The Lagoon">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|source-serif-4:400,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root, html[data-theme="light"] {
                --paper: #FBF7EF;
                --paper-raised: #FFFDF8;
                --ink: #1B1B1B;
                --muted-ink: #4A4A48;
                --lagoon: #0E6E6E;
                --lagoon-deep: #0A5454;
                --lagoon-pale: #DCEFEC;
                --ember: #B8603B;
                --hairline: #E8DFCB;
                --cream: #F3EADB;
                --accent: var(--ember);
                --accent-soft: var(--cream);
            }

            html[data-theme="dark"] {
                --paper: #141818;
                --paper-raised: #1B2020;
                --ink: #EDE6D6;
                --muted-ink: #A8A29B;
                --lagoon: #6FBEB5;
                --lagoon-deep: #4FA8A0;
                --lagoon-pale: #1E3A38;
                --ember: #E08862;
                --hairline: #2B2A27;
                --cream: #26221C;
                --accent: var(--ember);
                --accent-soft: var(--cream);
            }

            body {
                font-family: 'Space Grotesk', sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(251, 191, 36, 0.10), transparent 26rem),
                    radial-gradient(circle at bottom right, rgba(14, 110, 110, 0.10), transparent 28rem),
                    var(--paper);
                color: var(--ink);
                transition: background 200ms ease, color 200ms ease;
            }

            .font-display {
                font-family: 'Source Serif 4', serif;
            }
        </style>

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                if (stored === 'dark' || stored === 'light') {
                    document.documentElement.dataset.theme = stored;
                }
            })();
        </script>
    </head>
    <body class="min-h-screen antialiased">
        <div class="pointer-events-none fixed inset-0 bg-[linear-gradient(rgba(15,23,42,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(15,23,42,0.03)_1px,transparent_1px)] bg-[size:36px_36px]"></div>

        <div class="relative">
            <header class="border-b border-white/60 bg-white/55 backdrop-blur-xl">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
                    <a class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-900" href="{{ route('posts.index') }}">
                        Inkwell
                    </a>

                    <nav class="flex items-center gap-3 text-sm font-medium text-slate-600">
                        <a class="rounded-full border border-slate-200/80 px-4 py-2 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700" href="{{ route('posts.index') }}">
                            All Posts
                        </a>

                        <a class="rounded-full border border-slate-200/80 px-4 py-2 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700" href="{{ route('search') }}">
                            Search
                        </a>

                        <button type="button" onclick="(function(){const h=document.documentElement; const next = h.dataset.theme === 'dark' ? 'light' : 'dark'; h.dataset.theme = next; localStorage.setItem('theme', next);})()" class="rounded-full border border-slate-200/80 px-3 py-2 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700" aria-label="Toggle dark mode">
                            <span class="inline-block" aria-hidden="true">☾</span>
                        </button>

                        @auth
                            <a class="rounded-full border border-slate-200/80 px-4 py-2 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700" href="{{ route('posts.create') }}">
                                Write Post
                            </a>
                            <span class="px-2 text-xs font-semibold uppercase tracking-widest text-slate-400">
                                {{ auth()->user()->name }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-full border border-rose-200 px-4 py-2 text-rose-700 transition hover:border-rose-300 hover:bg-rose-50">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a class="rounded-full border border-slate-200/80 px-4 py-2 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700" href="{{ route('login') }}">
                                Login
                            </a>
                            <a class="rounded-full bg-slate-900 px-4 py-2 text-white transition hover:bg-slate-700" href="{{ route('register') }}">
                                Register
                            </a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-6 py-10">
                @if (session('status'))
                    <div class="mb-8 rounded-3xl border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="mt-16 border-t border-white/60 bg-white/55 backdrop-blur-xl">
                <div class="mx-auto flex max-w-6xl flex-wrap items-end justify-between gap-6 px-6 py-10">
                    <div class="max-w-md">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-900">The Lagoon</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">
                            A quiet blog about craft, rooms, and the things we keep nearby.
                            <a href="{{ route('feed') }}" class="font-semibold text-orange-700 hover:underline">RSS</a>.
                        </p>
                    </div>
                    <form action="{{ route('newsletter.store') }}" method="POST" class="flex w-full max-w-md flex-wrap items-stretch gap-2">
                        @csrf
                        <label for="newsletter-email" class="sr-only">Email</label>
                        <input
                            id="newsletter-email"
                            type="email"
                            name="email"
                            required
                            placeholder="you@quiet.email"
                            class="flex-1 min-w-[220px] rounded-full border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm outline-none focus:border-orange-300 focus:ring-4 focus:ring-orange-100"
                        >
                        <button type="submit" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Subscribe
                        </button>
                        <p class="w-full text-xs text-slate-500">A letter, twice a month. No algorithm.</p>
                    </form>
                </div>
            </footer>
        </div>
    </body>
</html>

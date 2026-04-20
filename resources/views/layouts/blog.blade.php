<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Inkwell Blog')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|source-serif-4:400,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --paper: #fffaf2;
                --ink: #1e293b;
                --accent: #c2410c;
                --accent-soft: #fed7aa;
            }

            body {
                font-family: 'Space Grotesk', sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(251, 191, 36, 0.16), transparent 26rem),
                    radial-gradient(circle at bottom right, rgba(194, 65, 12, 0.16), transparent 28rem),
                    linear-gradient(180deg, #fff7ed 0%, var(--paper) 45%, #f8fafc 100%);
                color: var(--ink);
            }

            .font-display {
                font-family: 'Source Serif 4', serif;
            }
        </style>
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
                        <a class="rounded-full bg-slate-900 px-4 py-2 text-white transition hover:bg-slate-700" href="{{ route('posts.create') }}">
                            Write Post
                        </a>
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
        </div>
    </body>
</html>

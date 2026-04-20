<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · @yield('title', 'Dashboard') · the lagoon</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=source-serif-4:600,700|space-grotesk:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
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
        }

        body {
            background: var(--paper);
            color: var(--ink);
            font-family: 'Space Grotesk', system-ui, sans-serif;
        }

        .serif { font-family: 'Source Serif 4', Georgia, serif; }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 border-r" style="background: var(--paper-raised); border-color: var(--hairline);">
            <div class="px-6 py-6 border-b" style="border-color: var(--hairline);">
                <a href="{{ route('admin.dashboard') }}" class="block">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.3em]" style="color: var(--ember);">The Lagoon</p>
                    <p class="serif mt-1 text-xl font-semibold" style="color: var(--ink);">Admin</p>
                </a>
            </div>
            <nav class="px-4 py-6 space-y-1 text-sm font-medium">
                @php $current = request()->route()?->getName(); @endphp
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 transition {{ $current === 'admin.dashboard' ? 'text-white' : '' }}" @class(['hover:bg-[var(--lagoon-pale)]']) style="{{ $current === 'admin.dashboard' ? 'background: var(--lagoon);' : 'color: var(--muted-ink);' }}">
                    Overview
                </a>
                <a href="{{ route('admin.posts.index') }}" class="block rounded-lg px-3 py-2 transition {{ str_starts_with($current ?? '', 'admin.posts') ? 'text-white' : '' }}" @class(['hover:bg-[var(--lagoon-pale)]']) style="{{ str_starts_with($current ?? '', 'admin.posts') ? 'background: var(--lagoon);' : 'color: var(--muted-ink);' }}">
                    Posts
                </a>
                <a href="{{ route('posts.index') }}" class="block rounded-lg px-3 py-2 transition hover:bg-[var(--lagoon-pale)]" style="color: var(--muted-ink);">
                    ← Back to site
                </a>
            </nav>
            <div class="px-6 mt-auto py-6 border-t" style="border-color: var(--hairline);">
                @auth
                    <p class="text-xs uppercase tracking-[0.24em]" style="color: var(--muted-ink);">Signed in</p>
                    <p class="serif mt-1 text-base font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--muted-ink);">{{ auth()->user()->role?->label() }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="text-xs font-semibold hover:underline" style="color: var(--ember);">Sign out</button>
                    </form>
                @endauth
            </div>
        </aside>

        <main class="flex-1 px-10 py-10">
            @if (session('status'))
                <div class="mb-6 rounded-xl border px-4 py-3 text-sm" style="background: var(--lagoon-pale); border-color: var(--lagoon); color: var(--lagoon-deep);">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>

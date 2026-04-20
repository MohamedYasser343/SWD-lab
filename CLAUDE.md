# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

**Initial setup:**
```bash
composer run setup
```

**Start development servers (Laravel + queue + Vite, concurrently):**
```bash
php artisan serve
```

**Run tests:**
```bash
php artisan test
```

**Run a single test file:**
```bash
php artisan test tests/Feature/ExampleTest.php
```

**Build frontend assets:**
```bash
npm run build
```

## Architecture

Laravel 13 blog application with SQLite, Blade templates, Tailwind CSS 4, and Vite.

**Request flow:** `routes/web.php` → `PostController` → `Post` model (Eloquent) → Blade views in `resources/views/posts/`.

**Posts resource** is the core domain:
- Route key binding uses `slug` (not `id`) — see `Post::getRouteKeyName()`
- `PostRequest` handles validation for create and update
- Slugs are auto-generated on store/update in `PostController`
- Index is paginated at 6 per page

**Root `/` redirects to `posts.index`.**

**Frontend:** Tailwind is configured via Vite (`vite.config.js`). CSS entry is `resources/css/app.css`; JS entry is `resources/js/app.js`. Blade layouts live in `resources/views/layouts/`.

**Database:** SQLite at `database/database.sqlite`. Sessions, cache, and queues all use the database driver (configured in `.env.example`).

**Testing:** PHPUnit with two suites — `Unit` (`tests/Unit/`) and `Feature` (`tests/Feature/`). Test environment uses an in-memory SQLite database (`phpunit.xml`).

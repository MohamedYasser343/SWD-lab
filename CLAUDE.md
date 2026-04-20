# CLAUDE.md

Guidance for Claude Code when working with this repository.

## Project summary

The **Cozy Lagoon** — a production-grade Laravel 13 blog with drafts, scheduled publishing, tags, likes, bookmarks, view counts, search, RSS, sitemap, newsletter double opt-in, role-based admin dashboard, and a warm paper-and-teal design system. The full specification lives in [docs/](docs/README.md).

## Commands

**Start development server:**
```bash
php artisan serve
```

**Run queue worker (for scheduled publishing + newsletter mail):**
```bash
php artisan queue:work
```

**Run scheduler (for auto-publishing scheduled posts):**
```bash
php artisan schedule:work
```

**Run tests:**
```bash
php artisan test
```

**Run a single test file:**
```bash
php artisan test tests/Feature/EngagementTest.php
```

**Fresh DB with seeded admin users:**
```bash
php artisan migrate:fresh --seed
```

Seed creates: `admin@example.com` (admin), `author@example.com` (author), `reader@example.com` (reader). Password: `password`.

**Build frontend assets:**
```bash
npm run build
```

## Architecture

**Style:** Layered modular monolith — MVC + Services/Actions. Full rationale in [docs/07-architecture.md](docs/07-architecture.md).

**Request flow:** `routes/web.php` → middleware (auth, admin, throttle) → controller → `FormRequest` (validation) → service/action → Eloquent model → DB → Blade view.

**Core domain:**
- `Post` — status enum (draft/published/scheduled/archived), slug route key, tags (m:n), featured image, SEO meta, reading time, views + likes counters. Scopes: `published()`, `scheduled()`, `draft()`, `forFeed()`.
- `User` — role enum (admin/author/reader), username route key for `/authors/{username}`.
- `Comment` — nested via `parent_id`, 3 levels deep.
- `Tag`, `Category`, `Like` (polymorphic), `Bookmark`, `PostView`, `NewsletterSubscriber`.

**Key services/jobs:**
- `App\Observers\PostObserver` — sets reading time, auto-`published_at` on status transitions, meta-description fallback.
- `App\Services\ViewRecorder` — dedupes post views by user (authed) or session (guest) per day.
- `App\Services\TableOfContentsExtractor` — parses H2/H3 from post body for the sticky TOC.
- `App\Jobs\PublishScheduledPostsJob` — runs every minute via the scheduler; flips due scheduled posts to published.

**Admin area (`/admin`):** custom Blade admin with Cozy theme, gated by `admin` middleware (`App\Http\Middleware\RequireAdminAccess`). Dashboard + posts list with status filter. Extensible — add more resources in `app/Http/Controllers/Admin/` and `resources/views/admin/`.

**Public routes:**
- `/` → redirects to `posts.index`
- `/posts` (index), `/posts/{slug}` (show with TOC + engagement)
- `/tags/{slug}`, `/authors/{username}`
- `/search?q=...` — LIKE-based full-text search over title/excerpt/body/tags
- `/feed.xml` (RSS 2.0), `/sitemap.xml`
- `/newsletter` (POST, throttled), `/newsletter/confirm/{token}`, `/newsletter/unsubscribe/{token}`
- `/posts/{post}/like` (POST, auth, throttled), `/posts/{post}/bookmark` (POST, auth, throttled)

**Frontend:** Tailwind 4 via Vite. Design tokens live in [resources/css/app.css](resources/css/app.css) under `@theme`. Dark mode via `html[data-theme]` swap, persisted to `localStorage`. Design system reference: [docs/08-design-system.md](docs/08-design-system.md).

**Database:** SQLite at `database/database.sqlite`. Sessions, cache, and queue all use the database driver. Production should switch to MySQL 8 + Redis for the queue (see `docs/07-architecture.md`).

**Testing:** PHPUnit. `RefreshDatabase` trait + `PostFactory` states (`draft()`, `scheduled()`, `withTags()`) and `UserFactory::admin()` / `::author()`. Test environment uses in-memory SQLite (`phpunit.xml`). Current suite: 37 tests, 105 assertions.

## Documentation

All architectural decisions, requirements, diagrams (ERD, class, sequence), use cases, and the Cozy Lagoon design system are in [docs/](docs/README.md). Keep docs in sync when you change behavior — every doc has a `**Last updated:**` line at the bottom.

## Cron note

For scheduled publishing to work in production, add this to the server's cron:

```
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

In local dev, `php artisan schedule:work` runs it in the foreground.

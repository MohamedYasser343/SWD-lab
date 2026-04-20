# 04 — Sequence Diagrams

This document shows the eight most important request flows of the Cozy Lagoon blog as Mermaid `sequenceDiagram` blocks. Each flow is annotated with the requirements it satisfies and the files that implement it.

Actors used across diagrams: `Browser`, `Nginx`, `Laravel` (HTTP kernel + controller), `Queue`, `DB`, `Mail`, `Storage`, `Cache`, `Scout`.

---

## 4.1 Registration with email verification

Satisfies FR-001, FR-004.

```mermaid
sequenceDiagram
    participant B as Browser
    participant L as Laravel
    participant DB
    participant Q as Queue
    participant M as Mail

    B->>L: POST /register (name,email,password)
    L->>L: RegisterRequest validation
    L->>DB: users.insert(role=reader)
    L->>Q: dispatch(SendEmailVerification)
    L-->>B: 302 /posts (session started)
    Q->>M: render + send verify mail
    B->>L: GET /email/verify/{token}
    L->>DB: users.update(email_verified_at=now)
    L-->>B: 200 verified page
```

---

## 4.2 Draft creation → publish transition

Satisfies FR-007, FR-008, FR-009, FR-011, FR-013.

```mermaid
sequenceDiagram
    participant B as Browser
    participant L as Laravel (PostController)
    participant R as PostRequest
    participant O as PostObserver
    participant DB
    participant S as Scout

    B->>L: POST /posts (title, body, status=draft)
    L->>R: prepareForValidation (slug, tags firstOrCreate)
    R->>L: validated data
    L->>DB: posts.insert(status=draft)
    O->>O: compute reading_minutes, meta fallback
    L-->>B: 302 /posts/{slug}

    Note over B,L: Later, author flips to published
    B->>L: PATCH /posts/{slug} (status=published, published_at=now)
    L->>DB: posts.update
    O->>S: searchable() re-index
    L-->>B: 302 /posts/{slug}
```

---

## 4.3 Scheduled publish (cron-driven)

Satisfies FR-009, FR-023, FR-024.

```mermaid
sequenceDiagram
    participant Cron
    participant Sch as Scheduler
    participant Q as Queue
    participant J as PublishScheduledPostsJob
    participant DB
    participant S as Scout
    participant C as Cache

    Cron->>Sch: every minute (schedule:run)
    Sch->>Q: dispatch PublishScheduledPostsJob
    Q->>J: handle()
    J->>DB: UPDATE posts SET status='published' WHERE status='scheduled' AND published_at<=now()
    J->>S: bulk searchable()
    J->>C: forget('feed.xml','sitemap.xml')
    J-->>Sch: done
```

---

## 4.4 Comment submission with rate limit + moderation

Satisfies FR-016, FR-020, FR-031, NFR-004.

```mermaid
sequenceDiagram
    participant B as Browser
    participant L as Laravel
    participant T as ThrottleMiddleware
    participant R as CommentRequest
    participant DB

    B->>L: POST /posts/{post}/comments (body, parent_id?)
    L->>T: throttle:5,1
    alt over limit
        T-->>B: 429 Too Many Requests
    else within limit
        T->>R: validate
        R->>L: validated data
        L->>DB: comments.insert(status=approved or pending)
        L-->>B: 302 back with flash
    end
```

---

## 4.5 Like toggle (optimistic UI, idempotent)

Satisfies FR-017, NFR-004.

```mermaid
sequenceDiagram
    participant B as Browser (Alpine)
    participant L as Laravel (LikeController)
    participant T as Throttle
    participant DB

    B->>B: optimistic: flip icon, inc count
    B->>L: POST /posts/{post}/like
    L->>T: throttle:30,1
    T->>L: ok
    L->>DB: likes.upsert or delete on user_id+likeable
    L->>DB: posts.likes_count = posts.likes()->count()
    L-->>B: 200 {liked: bool, count: int}
    B->>B: reconcile if divergent
```

---

## 4.6 Newsletter double opt-in

Satisfies FR-026, FR-027, FR-028.

```mermaid
sequenceDiagram
    participant B as Browser
    participant L as Laravel (NewsletterController)
    participant A as SubscribeToNewsletter action
    participant DB
    participant Q as Queue
    participant M as Mail
    participant E as Email client

    B->>L: POST /newsletter (email)
    L->>A: execute(email)
    A->>DB: subscribers.firstOrCreate(email, confirm_token)
    A->>Q: dispatch ConfirmNewsletterMail
    A-->>L: Subscriber
    L-->>B: 200 "check your inbox"
    Q->>M: send(ConfirmNewsletterMail)
    E->>B: user clicks link
    B->>L: GET /newsletter/confirm/{token}
    L->>DB: subscribers.update(confirmed_at=now)
    L-->>B: 200 "you're in"
```

---

## 4.7 Search query

Satisfies FR-022.

```mermaid
sequenceDiagram
    participant B as Browser
    participant L as Laravel (SearchController)
    participant S as Scout
    participant FTS as SQLite FTS5
    participant DB

    B->>L: GET /search?q=slowness
    L->>S: Post::search('slowness')->paginate(12)
    S->>FTS: SELECT rowid FROM posts_fts WHERE posts_fts MATCH ?
    FTS-->>S: ranked rowids
    S->>DB: SELECT * FROM posts WHERE id IN (...)
    DB-->>S: hydrated posts
    S-->>L: Paginator
    L-->>B: 200 rendered results
```

---

## 4.8 Image upload with variant generation

Satisfies FR-012.

```mermaid
sequenceDiagram
    participant B as Browser (Filament)
    participant L as Laravel
    participant ML as Medialibrary
    participant St as Storage
    participant Q as Queue
    participant I as Intervention
    participant DB

    B->>L: POST /admin/posts/{id} (featured file)
    L->>ML: addMediaFromRequest('featured')
    ML->>St: write original to media/{uuid}/original.jpg
    ML->>DB: media.insert
    ML->>Q: dispatch PerformConversions(thumb,card,hero)
    L-->>B: 200 form saved
    Q->>I: read original -> 3 resized variants
    I->>St: write thumb/card/hero
    Q->>DB: media.update(generated_conversions)
```

---

**Last updated:** 2026-04-20

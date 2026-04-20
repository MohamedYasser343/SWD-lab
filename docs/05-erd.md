# 05 — Entity Relationship Diagram

This document shows every persistent entity in the Cozy Lagoon blog and how they relate. The ERD is authoritative: if the code diverges from what is drawn here, one of them is a bug.

## Diagram

```mermaid
erDiagram
    USERS ||--o{ POSTS : authors
    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ LIKES : gives
    USERS ||--o{ BOOKMARKS : saves
    USERS ||--o{ REACTIONS : reacts
    USERS ||--o{ COMMENT_FLAGS : flags
    USERS ||--o{ POST_VIEWS : visits
    USERS }o--o{ ROLES : has
    ROLES }o--o{ PERMISSIONS : grants

    CATEGORIES ||--o{ POSTS : groups
    POSTS }o--o{ TAGS : "tagged via post_tag"
    POSTS ||--o{ COMMENTS : carries
    POSTS ||--o{ REACTIONS : receives
    POSTS ||--o{ BOOKMARKS : is_saved_as
    POSTS ||--o{ POST_VIEWS : counted_by
    POSTS ||--o{ MEDIA : "has (morph)"

    COMMENTS ||--o{ COMMENTS : "threads via parent_id"
    COMMENTS ||--o{ COMMENT_FLAGS : is_flagged_by

    LIKES }o--|| USERS : by
    LIKES }o--|| POSTS : "on (likeable poly)"

    NEWSLETTER_SUBSCRIBERS ||--|| NEWSLETTER_SUBSCRIBERS : "self (tombstone)"

    USERS {
        bigint id PK
        string username UK
        string name
        string email UK
        timestamp email_verified_at
        string password
        text bio
        string avatar
        string twitter
        string website
        bool   is_shadow_banned
        bool   dark_mode
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        text   description
        timestamp created_at
        timestamp updated_at
    }

    TAGS {
        bigint id PK
        string name
        string slug UK
        text   description
        timestamp created_at
        timestamp updated_at
    }

    POST_TAG {
        bigint post_id FK
        bigint tag_id  FK
    }

    POSTS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string title
        string slug UK
        string status "draft|published|scheduled|archived"
        timestamp published_at
        text   excerpt
        text   body
        string featured_image
        string meta_title
        string meta_description
        string og_image
        smallint reading_minutes
        int    views_count
        int    likes_count
        timestamp created_at
        timestamp updated_at
    }

    COMMENTS {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        bigint parent_id FK
        string status "pending|approved|rejected|spam"
        int    flags_count
        text   body
        timestamp created_at
        timestamp updated_at
    }

    LIKES {
        bigint id PK
        bigint user_id FK
        string likeable_type
        bigint likeable_id
        timestamp created_at
        timestamp updated_at
    }

    BOOKMARKS {
        bigint id PK
        bigint user_id FK
        bigint post_id FK
        timestamp created_at
        timestamp updated_at
    }

    REACTIONS {
        bigint id PK
        bigint user_id FK
        bigint post_id FK
        string emoji
        timestamp created_at
        timestamp updated_at
    }

    POST_VIEWS {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        string session_id
        string ip_hash
        timestamp viewed_at
    }

    COMMENT_FLAGS {
        bigint id PK
        bigint comment_id FK
        bigint user_id FK
        string reason
        timestamp created_at
        timestamp updated_at
    }

    NEWSLETTER_SUBSCRIBERS {
        bigint id PK
        string email UK
        timestamp confirmed_at
        timestamp unsubscribed_at
        string confirm_token
        string unsubscribe_token
        timestamp created_at
        timestamp updated_at
    }

    MEDIA {
        bigint id PK
        string model_type
        bigint model_id
        string collection_name
        string name
        string file_name
        string mime_type
        int    size
        json   custom_properties
        json   generated_conversions
        timestamp created_at
        timestamp updated_at
    }

    ROLES {
        bigint id PK
        string name UK "admin|author|reader"
    }

    PERMISSIONS {
        bigint id PK
        string name
    }
```

## Entity notes

### `users`

| Column | Notes |
|---|---|
| `username` | Unique, slug-safe, routes to `/authors/{username}`. |
| `email_verified_at` | Populated on verification click. Unverified users may read but not comment. |
| `is_shadow_banned` | When `true`, this user's comments render only to themselves. |
| `dark_mode` | Server-persisted preference for authed users; guests use `localStorage`. |

### `posts`

| Column | Notes |
|---|---|
| `status` | String enum; indexed for fast filtering on `/` and `/admin`. |
| `published_at` | Indexed; ordering key for the homepage. Future values indicate `scheduled`. |
| `reading_minutes` | Set by `PostObserver` on save; `ceil(word_count / 200)`. |
| `views_count`, `likes_count` | Denormalized counters for cheap renders; canonical values recomputable from tables. |
| `og_image` | Falls back to `featured_image`, then a site default paper-and-teal card. |

### `comments`

Threaded via `parent_id`. Cascades on post or user delete. `status` distinct from moderation `flags_count` — a comment can be `approved` and still flagged.

### `likes`

Polymorphic via `likeable_type` + `likeable_id` to reuse the same table for likes on posts *and* comments. Unique index on `(user_id, likeable_type, likeable_id)` makes the toggle idempotent.

### `bookmarks`

Deliberately **non-polymorphic** — only posts are bookmarkable and keeping the table simple avoids mixing semantics.

### `reactions`

Unique on `(user_id, post_id, emoji)`. Whitelisted emoji set enforced at the controller layer, not the DB — easier to tune.

### `post_views`

Deduplicated via unique index on `(post_id, session_id, DATE(viewed_at))`. `ip_hash` is a salted SHA-256 so we can count unique-ish views without storing PII.

### `newsletter_subscribers`

Self-relationship is not structural — `unsubscribed_at` tombstones a record so re-subscribing starts a fresh double opt-in cycle rather than silently re-enrolling.

### `media`

Managed by `spatie/laravel-medialibrary`. `generated_conversions` is a JSON map `{thumb: true, card: true, hero: true}` once the queue job finishes variant generation.

### `roles` / `permissions`

From `spatie/laravel-permission`. We only use roles for gating; permissions tables ship but we do not (yet) use them per-capability.

---

**Last updated:** 2026-04-20

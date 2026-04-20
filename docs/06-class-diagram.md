# 06 — Class Diagram

This document shows the application's PHP classes grouped into three subsystems. A single monolithic class diagram would be unreadable, so we split by concern: Domain, Policies + Requests, Services + Actions.

---

## 6.1 Domain models

Eloquent models and their relationships. Every model extends `Illuminate\Database\Eloquent\Model`.

```mermaid
classDiagram
    class User {
        +id
        +username
        +name
        +email
        +bio
        +avatar
        +is_shadow_banned: bool
        +dark_mode: bool
        +posts() HasMany
        +comments() HasMany
        +likes() HasMany
        +bookmarks() HasMany
        +avatarUrl() Attribute
        +hasRole(role) bool
    }

    class Post {
        +id
        +user_id
        +category_id
        +title
        +slug
        +status: PostStatus
        +published_at
        +excerpt
        +body
        +reading_minutes
        +views_count
        +likes_count
        +user() BelongsTo
        +category() BelongsTo
        +tags() BelongsToMany
        +comments() HasMany
        +likes() MorphMany
        +reactions() HasMany
        +bookmarks() HasMany
        +views() HasMany
        +scopePublished(q)
        +scopeScheduled(q)
        +scopeDraft(q)
        +scopeForFeed(q)
        +readingTime() Attribute
        +tableOfContents() Attribute
        +ogImageUrl() Attribute
        +publish()
        +toSearchableArray() array
        +registerMediaConversions()
    }

    class Comment {
        +id
        +post_id
        +user_id
        +parent_id
        +status: CommentStatus
        +flags_count
        +body
        +post() BelongsTo
        +user() BelongsTo
        +parent() BelongsTo
        +replies() HasMany
        +flags() HasMany
        +scopeApproved(q)
        +scopeVisibleTo(q, user)
    }

    class Category {
        +id
        +name
        +slug
        +description
        +posts() HasMany
    }

    class Tag {
        +id
        +name
        +slug
        +posts() BelongsToMany
    }

    class Reaction {
        +id
        +user_id
        +post_id
        +emoji
        +user() BelongsTo
        +post() BelongsTo
    }

    class Like {
        +id
        +user_id
        +likeable_type
        +likeable_id
        +likeable() MorphTo
        +user() BelongsTo
    }

    class Bookmark {
        +id
        +user_id
        +post_id
        +user() BelongsTo
        +post() BelongsTo
    }

    class NewsletterSubscriber {
        +id
        +email
        +confirmed_at
        +unsubscribed_at
        +confirm_token
        +unsubscribe_token
        +isConfirmed() bool
        +isTombstoned() bool
    }

    class CommentFlag {
        +id
        +comment_id
        +user_id
        +reason
        +comment() BelongsTo
        +user() BelongsTo
    }

    class PostView {
        +post_id
        +session_id
        +user_id
        +ip_hash
        +viewed_at
    }

    User "1" --> "*" Post : authors
    User "1" --> "*" Comment : writes
    User "1" --> "*" Like : gives
    User "1" --> "*" Bookmark : saves
    User "1" --> "*" Reaction : reacts
    Category "1" --> "*" Post : groups
    Post "*" --> "*" Tag : tagged
    Post "1" --> "*" Comment : carries
    Post "1" --> "*" Reaction : receives
    Post "1" --> "*" Bookmark : is_saved
    Post "1" --> "*" PostView : counted
    Comment "1" --> "*" Comment : parent
    Comment "1" --> "*" CommentFlag : flagged
```

---

## 6.2 Policies and Form Requests

Authorization and validation. Each `*Policy` extends nothing (Laravel discovers via convention); each `*Request` extends `Illuminate\Foundation\Http\FormRequest`.

```mermaid
classDiagram
    class FormRequest

    class PostRequest {
        +authorize() bool
        +rules() array
        +prepareForValidation()
        +messages() array
    }

    class CommentRequest {
        +authorize() bool
        +rules() array
        +prepareForValidation()
    }

    class NewsletterSubscribeRequest {
        +rules() array
    }

    class CommentFlagRequest {
        +rules() array
    }

    class ProfileUpdateRequest {
        +rules() array
    }

    class PostPolicy {
        +viewAny(User) bool
        +view(User, Post) bool
        +create(User) bool
        +update(User, Post) bool
        +delete(User, Post) bool
        +publish(User, Post) bool
    }

    class CommentPolicy {
        +create(User, Post) bool
        +delete(User, Comment) bool
        +approve(User, Comment) bool
        +reject(User, Comment) bool
        +flag(User, Comment) bool
    }

    class TagPolicy {
        +create(User) bool
        +update(User, Tag) bool
        +delete(User, Tag) bool
    }

    class NewsletterPolicy {
        +viewAny(User) bool
        +delete(User) bool
    }

    FormRequest <|-- PostRequest
    FormRequest <|-- CommentRequest
    FormRequest <|-- NewsletterSubscribeRequest
    FormRequest <|-- CommentFlagRequest
    FormRequest <|-- ProfileUpdateRequest
```

---

## 6.3 Services and Actions

Domain logic that does not belong on a model. Services are long-lived singletons; actions are invocable classes for a single use case.

```mermaid
classDiagram
    class ReadingTimeCalculator {
        +compute(body: string) int
    }

    class TableOfContentsExtractor {
        -parser: CommonMarkParser
        +extract(body: string) TocNode[]
    }

    class TocNode {
        +level: int
        +text: string
        +anchor: string
        +children: TocNode[]
    }

    class ImageVariantService {
        +generate(Media) void
        +urlFor(Post, variant) string
    }

    class ViewRecorder {
        -cache: CacheRepository
        +record(Post, Request) void
    }

    class SubscribeToNewsletter {
        +__invoke(email: string) NewsletterSubscriber
    }

    class ConfirmNewsletterSubscription {
        +__invoke(token: string) bool
    }

    class SearchService {
        +query(q: string, perPage: int) Paginator
    }

    class PublishScheduledPostsJob {
        +handle() void
    }

    class PostObserver {
        +saving(Post) void
        +saved(Post) void
        +deleting(Post) void
    }

    TableOfContentsExtractor --> TocNode : builds
    PostObserver --> ReadingTimeCalculator : uses
    PublishScheduledPostsJob --> SearchService : reindexes
    SubscribeToNewsletter --> ConfirmNewsletterSubscription : paired_with
```

### Method notes

| Method | Contract |
|---|---|
| `ReadingTimeCalculator::compute($body)` | Strip HTML → split on whitespace → `ceil(count / 200)`. Returns at least `1`. |
| `TableOfContentsExtractor::extract($body)` | Walks CommonMark AST, emits only `H2`/`H3`, auto-generates slug anchors, nests H3 under H2. |
| `ImageVariantService::generate(Media $m)` | Dispatches three queued conversions; no-op if already generated. |
| `ViewRecorder::record(Post, Request)` | Cache key `view:{post_id}:{session_id}:{yyyymmdd}` with TTL 24h; increments only on first hit. |
| `SubscribeToNewsletter($email)` | Idempotent: re-entering a confirmed email returns the existing subscriber; re-entering a tombstoned email creates a fresh pending record. |
| `PublishScheduledPostsJob::handle()` | Single SQL UPDATE + bulk `searchable()` + cache-forget of feed/sitemap. Idempotent, safe to run every minute. |
| `PostObserver::saving($post)` | Sets `reading_minutes`, auto-sets `published_at` on draft→published transitions, fills missing `meta_description` from excerpt. |

---

**Last updated:** 2026-04-20

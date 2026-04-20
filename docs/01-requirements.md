# 01 — Requirements

This document enumerates the functional and non-functional requirements for the Cozy Lagoon blog. It is the anchor every other doc traces back to.

## Overview

Cozy Lagoon is a single-tenant blog platform for one publishing team (admin + authors) and an unlimited audience of readers and subscribers. It balances a warm, paper-textured reading experience against a disciplined authoring workflow (drafts, scheduling, moderation, analytics).

## Scope

**In scope**

- Public blog: browse, read, search, subscribe, react, comment.
- Authoring: draft, schedule, tag, upload images, SEO metadata, author profile.
- Administration: Filament panel for content, users, tags, comments, newsletter.
- Distribution: RSS feed, XML sitemap, OpenGraph/Twitter cards.
- Engagement: likes, bookmarks, emoji reactions, threaded comments with moderation.

**Out of scope**

- Multi-tenant / white-label publishing.
- Paid memberships, gated content.
- Native mobile apps.
- Real-time collaboration on drafts.
- AI-generated content.

## Assumptions

- Traffic: up to ~50 k monthly visits; a single VPS is sufficient.
- Team: 1 admin, up to 10 authors, unlimited readers.
- Browsers: last two versions of evergreen (Chrome, Firefox, Safari, Edge); graceful degradation below.
- Timezone: content is authored in `Africa/Cairo`; displayed to each reader in their local time.
- Content is primarily long-form prose in Markdown.

## Glossary

| Term           | Meaning                                                                                            |
| -------------- | -------------------------------------------------------------------------------------------------- |
| **Post**       | A single article: title, slug, body (Markdown), optional featured image, tags, category, SEO meta. |
| **Draft**      | A post that is not yet visible to the public.                                                      |
| **Scheduled**  | A post with a future `published_at` that the scheduler will publish automatically.                 |
| **Reader**     | An unauthenticated visitor, or a logged-in user with the `reader` role.                            |
| **Author**     | A logged-in user with the `author` role; may create and manage their own posts.                    |
| **Admin**      | A logged-in user with the `admin` role; may manage anything, including users.                      |
| **Subscriber** | An email address that has confirmed newsletter signup.                                             |
| **Reaction**   | A lightweight emoji response on a post (distinct from a like).                                     |
| **Shadow-ban** | A moderation state where a user's comments are hidden from everyone except themselves.             |

## Functional requirements

### Auth & profile

| ID     | Requirement                                                                                                           |
| ------ | --------------------------------------------------------------------------------------------------------------------- |
| FR-001 | A visitor can register with name, email, password. Email must be unique and verified.                                 |
| FR-002 | A registered user can log in and log out.                                                                             |
| FR-003 | A user can request a password reset via email token.                                                                  |
| FR-004 | The system supports three roles — admin, author, reader — assigned via `spatie/laravel-permission`.                   |
| FR-005 | Each user has a public profile at `/authors/{username}` showing bio, avatar, social links, and their published posts. |
| FR-006 | A user can update their profile (bio, avatar, social, dark-mode preference).                                          |

### Content

| ID     | Requirement                                                                                                                   |
| ------ | ----------------------------------------------------------------------------------------------------------------------------- |
| FR-007 | An author can create, update, and delete their own posts.                                                                     |
| FR-008 | A post has a status of `draft`, `published`, `scheduled`, or `archived`.                                                      |
| FR-009 | Setting `published_at` to a future time automatically sets status to `scheduled`; a job publishes it when due.                |
| FR-010 | Each post belongs to one category and many tags (m:n).                                                                        |
| FR-011 | The system auto-generates the slug from the title if left blank (existing behavior, preserved).                               |
| FR-012 | An author can upload a featured image; the system stores `thumb` (320×200), `card` (640×400), and `hero` (1280×720) variants. |
| FR-013 | Reading time is computed on save as `ceil(word_count / 200)` minutes.                                                         |
| FR-014 | A table of contents is extracted from `H2`/`H3` headings at render time.                                                      |
| FR-015 | Each post has SEO fields: `meta_title`, `meta_description`, `og_image` (falls back to featured image, then site default).     |

### Engagement

| ID     | Requirement                                                                            |
| ------ | -------------------------------------------------------------------------------------- |
| FR-016 | Logged-in users can submit threaded comments (up to 3 levels deep — current behavior). |
| FR-017 | Logged-in users can like any post (polymorphic, idempotent).                           |
| FR-018 | Logged-in users can bookmark posts for later reading.                                  |
| FR-019 | Logged-in users can leave emoji reactions from a fixed whitelist (👍 ❤️ 😂 😮 😢 🙌).  |
| FR-020 | Any user can flag a comment; flags accumulate on the comment.                          |
| FR-021 | Post view counts are deduplicated per session per day.                                 |

### Discovery

| ID     | Requirement                                                                                    |
| ------ | ---------------------------------------------------------------------------------------------- |
| FR-022 | A reader can search published posts by full-text across title, excerpt, body, and tag names.   |
| FR-023 | The site exposes `/feed.xml` (RSS 2.0) of the latest 20 published posts.                       |
| FR-024 | The site exposes `/sitemap.xml` listing all published posts, tags, authors, and the home page. |
| FR-025 | Each post has correct OpenGraph + Twitter Card meta tags.                                      |

### Newsletter

| ID     | Requirement                                                                                                                  |
| ------ | ---------------------------------------------------------------------------------------------------------------------------- |
| FR-026 | A visitor can subscribe to the newsletter by email.                                                                          |
| FR-027 | Subscription uses double opt-in: a confirmation email with a signed token must be clicked within 48 hours.                   |
| FR-028 | Every newsletter email contains a one-click unsubscribe token link.                                                          |
| FR-029 | Unsubscribed addresses are tombstoned, not deleted (so we do not re-send if they re-subscribe accidentally in the same day). |

### Administration

| ID     | Requirement                                                                                                                   |
| ------ | ----------------------------------------------------------------------------------------------------------------------------- |
| FR-030 | An admin can manage posts, users, comments, tags, categories, and newsletter subscribers from the Filament panel at `/admin`. |
| FR-031 | An admin can approve, reject, or spam-mark comments in a moderation queue.                                                    |
| FR-032 | An admin can shadow-ban a user; their comments then render only to themselves.                                                |
| FR-033 | The admin dashboard surfaces stats: total posts, drafts, scheduled, 7-day views, subscribers, posts needing moderation.       |
| FR-034 | An admin can export newsletter subscribers as CSV.                                                                            |

## Non-functional requirements

| ID      | Category        | Requirement                                                                                                                   |
| ------- | --------------- | ----------------------------------------------------------------------------------------------------------------------------- |
| NFR-001 | Performance     | Largest Contentful Paint ≤ 2.0 s on a public post page over 3G Fast (Lighthouse).                                             |
| NFR-002 | Performance     | p95 server response time ≤ 300 ms for cached reads.                                                                           |
| NFR-003 | Security        | All mutating requests use CSRF tokens; auth uses bcrypt; passwords never logged.                                              |
| NFR-004 | Security        | Rate limits: comments `5/min`, likes/reactions `30/min`, newsletter signup `3/min`.                                           |
| NFR-005 | Accessibility   | Meets WCAG 2.1 AA: contrast ≥ 4.5:1 body, ≥ 3:1 large text, visible focus on every interactive element.                       |
| NFR-006 | SEO             | Every post ships canonical URL, structured data (`Article`), and appears in `/sitemap.xml` within 1 minute of publishing.     |
| NFR-007 | Scalability     | Background work (mail, image variants, Scout indexing) runs through the database queue; no blocking HTTP call exceeds 500 ms. |
| NFR-008 | Maintainability | Code follows PSR-12; Pint-formatted; feature tests cover ≥ 70 % of controllers and services.                                  |
| NFR-009 | Privacy         | Newsletter signups require explicit opt-in; data is retained until unsubscribed or 36 months, whichever is sooner.            |
| NFR-010 | Observability   | All errors are logged with request context; Telescope is enabled in local, disabled in production.                            |
| NFR-011 | UX              | The site supports dark mode, persisted to `localStorage` for guests and to `users.dark_mode` for authed users.                |
| NFR-012 | i18n-ready      | All user-facing strings are translatable via Laravel `__()` helper (English ships first).                                     |

## Traceability hints

Each sequence diagram in [04](04-sequence-diagrams.md) is annotated with the FR(s) it satisfies. Each use case in [02](02-use-cases.md) references its FRs in its footer. When a requirement changes, grep for its ID (`FR-007`) across `docs/` to find every consumer.

---

**Last updated:** 2026-04-20

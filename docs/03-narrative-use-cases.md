# 03 — Narrative Use Cases

This document tells five stories of real people using the Cozy Lagoon blog. Where [02](02-use-cases.md) is a catalog, this is a film strip — it is where we check that the features hang together as a lived experience.

---

## 1. Layla discovers the blog

Layla is on her lunch break, scrolling for something to read that is not a news feed. A friend sent her a link: `thelagoon.blog/posts/on-writing-slowly`. The page loads onto a soft cream background; the headline is set in a serif that feels like a letter. She reads half the piece standing in line for coffee.

A small TOC follows her as she scrolls; when she jumps to a later section, the active heading quietly highlights. At the bottom, she taps the 🙌 reaction — a little animation, nothing loud. She notices a bookmark icon and a newsletter form that reads "A letter, twice a month. No algorithm." She enters her email, gets a confirmation mail, clicks the link, and is told, simply, "You're in."

Back at her desk she signs up for a reader account — it takes two fields — and bookmarks two more posts from the related-reading strip. When she returns on the weekend, her bookmarked list is waiting for her on her profile.

**Features exercised:** [FR-003](01-requirements.md#functional-requirements), FR-013, FR-014, FR-017, FR-018, FR-019, FR-026, FR-027, FR-028, FR-005, FR-015 (OG card on the original shared link).

---

## 2. Omar drafts a post and schedules it for Sunday morning

Omar is an author. He has a half-written idea about why certain kitchens feel quiet. He logs in, opens `/admin`, and creates a new post. He writes the first paragraph, adds `#craft` and `#notes` as tags (typing them comma-separated — the system creates `#notes` on the fly because it is new), and leaves the post as a draft.

Two days later he finishes the piece. He drops in a photograph of his kitchen window; the system generates three variants (thumb, card, hero) in the background while he keeps writing. In the SEO tab he enters a tighter meta description. He clicks "Schedule", picks `Sunday 07:00 Africa/Cairo`, and saves. The status chip flips to `scheduled`, showing "Publishes in 2 days, 14 hours."

Sunday morning at 07:01 the post goes live. Omar finds out when a friend texts him a screenshot. He had slept through it.

**Features exercised:** FR-007, FR-008, FR-009, FR-010, FR-011, FR-012, FR-013, FR-015, FR-030.

---

## 3. The system publishes at dawn

At 07:00:00 the Laravel scheduler fires `schedule:run`. `PublishScheduledPostsJob` dispatches to the queue. The worker picks it up, selects posts where `status = scheduled AND published_at <= now()`, and in a transaction flips each to `status = published`. It re-indexes the affected posts into Scout's SQLite FTS virtual table and pings `/sitemap.xml` to regenerate.

No one sees this. But by 07:00:03, a subscriber's RSS reader polls `/feed.xml` and finds the new entry. A visitor who was already on the homepage, if they refresh, sees the post at the top of the grid, with a fresh-paper glow the design system reserves for the last 24 hours.

**Features exercised:** FR-009, FR-023, FR-024, NFR-007.

---

## 4. Nadia moderates a flagged comment

Nadia is the admin. Her morning routine opens with `/admin`. The `Recent Comments` widget on her dashboard has a red pip: three comments flagged overnight.

She opens the moderation queue. The first flagged comment is a respectful disagreement — she clicks **Approve**. The second is spam linking to a sketchy store — **Reject**, then a click on the author's name takes her to the user page where she toggles **Shadow-ban**. That user's past and future comments disappear for every reader except the user themselves; the user will have no idea anything changed, which is the point.

The third flag is on a comment that turned out to be a flag-bombing attempt by a rival account. Nadia dismisses the flags, opens the flagger's profile, and notes the pattern. She will watch.

Total time: under four minutes. The queue is empty. She writes her own post.

**Features exercised:** FR-020, FR-031, FR-032, FR-033, FR-034, NFR-004.

---

## 5. Youssef unsubscribes

Youssef subscribed a year ago. He still reads the blog from time to time, but his inbox has grown cluttered. At the bottom of today's newsletter he clicks **Unsubscribe**. The link carries a signed `unsubscribe_token`; the page that loads says, simply, "Done. You will not hear from us again." There is no multi-step retention flow, no "are you sure", no dark pattern.

Server-side, the subscriber record is tombstoned — `unsubscribed_at` set, `confirmed_at` cleared. If Youssef were to subscribe again tomorrow the system would treat it as a new signup, starting over with double opt-in, because the tombstone prevents silent re-enrollment but does not block a genuine second choice.

A week later he reads a post he loved and shared it with a friend. He does not subscribe again. That is fine. Cozy Lagoon would rather have a small list of readers who want to be there.

**Features exercised:** FR-028, FR-029, NFR-009.

---

**Last updated:** 2026-04-20

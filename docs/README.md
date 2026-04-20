# Cozy Lagoon — Documentation

A warm, reading-first blog built on Laravel 13. This folder is the single source of truth for what the product is, why it exists, and how it is built.

## At a glance

The Cozy Lagoon is a production-grade, single-tenant blog platform. It pairs a quiet, paper-and-teal reading experience with a full authoring toolkit: drafts, scheduling, tags, featured images, search, newsletters, reactions, and a Filament-powered admin panel. The aim is a site that feels hand-made to read, yet disciplined to maintain.

## Tech stack

| Layer | Choice | Why |
|---|---|---|
| Runtime | PHP 8.3, Laravel 13 | Batteries-included, ships with queues, scheduler, mail, validation |
| Frontend | Blade + Alpine.js + Tailwind 4 | Server-first rendering, minimal JS, Cozy Lagoon design system |
| Admin | Filament v4 | Fast RBAC-aware admin, themeable to match the public site |
| Database | SQLite (dev) / MySQL 8 (prod) | Simple local dev; scales when needed |
| Search | Laravel Scout + SQLite FTS5 | Zero-dependency full-text search; swap to Meilisearch later |
| Storage | `public` disk + spatie/medialibrary | Image variants (thumb/card/hero) generated on upload |
| Mail | `log` (dev) / SMTP (prod) | Queued via database queue |
| Auth | Custom controllers + spatie/laravel-permission | Three roles: admin, author, reader |

## Documentation map

Read the docs in the suggested order (1 → 2 → 3 → 5 → 6 → 4 → 7 → 8) to build up from **what** to **how** to **why**.

| # | Document | Purpose | Audience |
|---|---|---|---|
| 01 | [Requirements](01-requirements.md) | Functional + non-functional requirements, scope, glossary | Everyone |
| 02 | [Use Cases](02-use-cases.md) | Actors, use-case diagram, 15 UC cards | Product, engineering |
| 03 | [Narrative Use Cases](03-narrative-use-cases.md) | Five day-in-the-life stories tying features together | Everyone |
| 04 | [Sequence Diagrams](04-sequence-diagrams.md) | 8 flows from registration to image variant generation | Engineering |
| 05 | [ERD](05-erd.md) | Every table, relationship, and column note | Engineering, DBAs |
| 06 | [Class Diagram](06-class-diagram.md) | Models, services, policies, requests | Engineering |
| 07 | [Architecture](07-architecture.md) | Chosen style, component diagram, deployment, trade-offs | Engineering, reviewers |
| 08 | [Design System](08-design-system.md) | Tokens, components, mockups for Cozy Lagoon | Designers, frontend |

## How to read these docs

- Diagrams are written as [Mermaid](https://mermaid.js.org/) fenced code blocks. They render natively on GitHub and in VS Code with the Mermaid extension.
- Cross-document links are relative: `[FR-007](01-requirements.md#fr-007)`.
- Every doc opens with a two-sentence purpose line and closes with `**Last updated:**` so you can spot stale sections.
- If something here contradicts the code, the code wins — please file a docs PR.

## Conventions

- Colors appear with their hex on first mention: `lagoon #0E6E6E`.
- Mermaid diagrams stay under ~25 nodes; split when they grow.
- Times and dates are absolute (`2026-04-20`), never relative (`last week`).
- Tables for enumerations, numbered lists for flows, prose for rationale.

## Contributing

1. Edit the relevant `.md` file in place.
2. If you touch a diagram, preview it before committing.
3. Bump the `Last updated:` line on every file you edit.
4. Run `php artisan test` and `npm run build` before opening a PR.

---

**Last updated:** 2026-04-20

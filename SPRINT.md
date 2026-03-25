# KLP48 Website Redesign - Agile Sprint Tracker

## Project: Jade Blossom Redesign

**Start Date:** 2026-03-24
**Tech Stack:** Vue 3 + TypeScript + TailwindCSS + GSAP + Vite (Frontend) | Laravel 12 (Backend - Phase 2)

---

## Sprint 1: Project Scaffolding & Design System

**Status:** Done
**Goal:** Set up the frontend project with the Jade Blossom design system, GSAP, and real KLP48 data.

| #   | Task                                                          | Status |
| --- | ------------------------------------------------------------- | ------ |
| 1.1 | Create `frontend/` with Vite + Vue 3 + TS                     | Done   |
| 1.2 | Configure Tailwind (Jade Blossom palette, typography)         | Done   |
| 1.3 | Install GSAP, create `useGsap` composable                     | Done   |
| 1.4 | Create base UI components (buttons, badges, pill toggles)     | Done   |
| 1.5 | Define TypeScript types (member, news, release, video, event) | Done   |
| 1.6 | Populate JSON data with real KLP48 data                       | Done   |
| 1.7 | Set up utils (constants, helpers)                             | Done   |

---

## Sprint 2: Layout & Navigation

**Status:** Done
**Goal:** Build the shell — header, footer, mobile menu, router, and page transitions.

| #   | Task                                             | Status |
| --- | ------------------------------------------------ | ------ |
| 2.1 | Header (frosted-glass jade, scroll transition)   | Done   |
| 2.2 | MobileMenu (full-screen overlay, batik BG)       | Done   |
| 2.3 | Footer (sister group marquee, sponsors, socials) | Done   |
| 2.4 | Language switcher (pill toggles)                 | Done   |
| 2.5 | Vue Router setup with all routes                 | Done   |
| 2.6 | GSAP page transitions                            | Done   |

---

## Sprint 3: Home Page & Core Sections

**Status:** Done
**Goal:** Build the homepage with hero, ticker, and preview sections.

| #   | Task                                                          | Status  |
| --- | ------------------------------------------------------------- | ------- |
| 3.1 | Hero Section (jade gradient BG, arch frame, sakura particles) | Done    |
| 3.2 | Announcement Ticker (horizontal scroll, gold border)          | Done    |
| 3.3 | News preview section (cards, jade border, batik hover)        | Done    |
| 3.4 | Member preview section (colored frames, generation labels)    | Done    |
| 3.5 | Release preview section (horizontal carousel)                 | Done    |
| 3.6 | Loading screen (logo + jade pulse)                            | Pending |

---

## Sprint 4: Full Pages

**Status:** Done
**Goal:** Build all route pages with full functionality.

| #   | Task                                              | Status |
| --- | ------------------------------------------------- | ------ |
| 4.1 | News page + NewsDetail page                       | Done   |
| 4.2 | Members page + MemberDetail page                  | Done   |
| 4.3 | Releases/Discography page (vinyl reveal on hover) | Done   |
| 4.4 | Videos page (modal player, type filters)          | Done   |
| 4.5 | Schedule page (timeline view, status dots)        | Done   |
| 4.6 | Fanclub page (CTA card, benefits grid)            | Done   |
| 4.7 | About page (milestones, 48 Group family)          | Done   |
| 4.8 | 404 Not Found page                                | Done   |

---

## Sprint 5: Polish & Animations

**Status:** In Progress
**Goal:** Add GSAP animations, responsive fixes, and performance optimization.

| #   | Task                                         | Status  |
| --- | -------------------------------------------- | ------- |
| 5.1 | Scroll-triggered GSAP animations (all pages) | Done    |
| 5.2 | Member card hover effects                    | Done    |
| 5.3 | Release card vinyl/CD reveal                 | Done    |
| 5.4 | Hero sakura petal particles                  | Done    |
| 5.5 | Batik pattern hover overlays                 | Done    |
| 5.6 | Responsive testing & fixes                   | Pending |
| 5.7 | Performance optimization                     | Pending |

---

## Sprint 6: Backend Scaffolding & Database

**Status:** Done
**Goal:** Scaffold Laravel 12, configure PostgreSQL, create migrations and models for all content types.

| #   | Task                                                        | Status |
| --- | ----------------------------------------------------------- | ------ |
| 6.1 | Scaffold Laravel 12 (v13.1) in `backend/`                  | Done   |
| 6.2 | Configure PostgreSQL (port 5434, DB: klp48)                 | Done   |
| 6.3 | Create migrations (members, news, releases, videos, events) | Done   |
| 6.4 | Create Eloquent models with scopes                          | Done   |
| 6.5 | Create seeders with real KLP48 data                         | Done   |
| 6.6 | Run migrations & seeders, verify DB (16+11+5+10+6 rows)    | Done   |

---

## Sprint 7: API Endpoints & Frontend Integration

**Status:** Done
**Goal:** Build RESTful API endpoints and connect frontend to fetch from backend API.

| #   | Task                                                    | Status |
| --- | ------------------------------------------------------- | ------ |
| 7.1 | Create API resource classes (camelCase transformers)     | Done   |
| 7.2 | Members API (list, detail, filter by generation/status)  | Done   |
| 7.3 | News API (list, detail, filter by category)              | Done   |
| 7.4 | Releases API (list, filter by type)                      | Done   |
| 7.5 | Videos API (list, filter by type)                        | Done   |
| 7.6 | Events/Schedule API (list, filter by type/status)        | Done   |
| 7.7 | API route registration & CORS config                     | Done   |
| 7.8 | Create `useApi` composable & `apiFetch` helper           | Done   |
| 7.9 | Update all frontend pages to fetch from backend API      | Done   |
| 7.10| Remove static JSON data files from frontend              | Done   |

---

## Sprint 8: Admin Panel

**Status:** Done
**Goal:** Admin panel for content management with Filament v4.

| #   | Task                                                       | Status |
| --- | ---------------------------------------------------------- | ------ |
| 8.1 | Install Filament v4 (compatible with Laravel 13)           | Done   |
| 8.2 | Configure AdminPanelProvider with Teal theme               | Done   |
| 8.3 | User model implements FilamentUser                         | Done   |
| 8.4 | MemberResource (form + table + filters)                    | Done   |
| 8.5 | NewsResource (form + table + filters)                      | Done   |
| 8.6 | ReleaseResource (form + table + repeater tracks)           | Done   |
| 8.7 | VideoResource (form + table + filters)                     | Done   |
| 8.8 | EventResource (form + table + filters)                     | Done   |
| 8.9 | Image upload handling (FileUpload, storage symlink)        | Done   |
| 8.10| End-to-end verification (API + admin panel + frontend TS)  | Done   |

---

## Sprint 9: Multi-Language (i18n) Support

**Status:** Done
**Goal:** Add English/Malay/Japanese language support with auto-translation and admin management.

| #   | Task                                                        | Status |
| --- | ----------------------------------------------------------- | ------ |
| 9.1 | Create `translations` table (polymorphic, auto_translated)  | Done   |
| 9.2 | Translation model + HasTranslations trait for all models    | Done   |
| 9.3 | TranslationService (LibreTranslate + MyMemory fallback)     | Done   |
| 9.4 | Auto-translate observer on content create/update            | Done   |
| 9.5 | API resources merge translations via `?lang=` param         | Done   |
| 9.6 | Filament TranslationResource (list, edit, filter)           | Done   |
| 9.7 | Auto Translate button on all content edit pages             | Done   |
| 9.8 | Install vue-i18n, create EN/MS/JP locale JSON files         | Done   |
| 9.9 | Replace all hardcoded UI strings with `$t()` calls          | Done   |
| 9.10| Language selector dropdown (flags: 🇬🇧 EN, 🇲🇾 MY, 🇯🇵 JP) | Done   |
| 9.11| API requests auto-append `?lang=` from language store       | Done   |
| 9.12| Remove zh-CN, sync vue-i18n locale with Pinia store        | Done   |

---

## Build Verification (2026-03-24)

- `npm install` - 257 packages installed (added vue-i18n)
- `vue-tsc --noEmit` - TypeScript passes clean (0 errors)
- `vite build` - Production build succeeds in ~2.3s
- All 11 routes created and code-split
- GSAP ScrollTrigger integrated on all pages
- i18n: 3 locale files (EN/MS/JP), ~125 translated UI strings

## Notes

- Real KLP48 data used where accessible from klp48.my (news, releases, videos, sponsors, sister groups)
- Dummy data used for: member profiles, schedule events, about content, fanclub tiers
- GSAP used for all animations (no framer-motion)
- Design follows "Jade Blossom" concept from KLP48-Website-Redesign.md
- Color palette: Jade #00B4A0, Sakura #F4A7BB, Gold #C9A84C, Charcoal #1A1A2E, Cream #FFF8F0
- i18n uses LibreTranslate (primary) + MyMemory (fallback) for auto-translation — Malay uses `ms` locale (not Indonesian `id`)
- Manual translations in admin panel are preserved when auto-translate runs (auto_translated flag)

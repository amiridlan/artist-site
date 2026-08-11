# KLP48 Website Redesign — Design Document

## Design Concept: _"Jade Blossom"_

The core identity of KLP48 is rooted in **Malaysian jade** — their official brand color symbolizing gentle aura, empathy, and the fusion of cultures. This redesign weaves together **Malay Islamic geometric art** (inspired by batik, songket weaving, and mosque tile patterns) with **Japanese aesthetic sensibility** (wabi-sabi minimalism, sakura motifs, ukiyo-e-influenced illustration style). The result should feel like a premium cultural artifact — not a generic idol fansite.

---

## 1. Visual Identity & Theme

### Color Palette

| Role               | Color                | Hex Code  | Reference                                          |
| ------------------ | -------------------- | --------- | -------------------------------------------------- |
| Primary (light)    | Malaysian Jade Green | `#00B4A0` | KLP48 brand jade                                   |
| Primary (dark)     | Deep Jade            | `#006B5E` | Gradient endpoint                                  |
| Accent warm        | Sakura Pink          | `#F4A7BB` | Japanese cherry blossom                            |
| Accent metallic    | Songket Gold         | `#C9A84C` | Traditional gold thread                            |
| Neutral dark       | Deep Charcoal        | `#1A1A2E` | Text & dark backgrounds                            |
| Neutral light      | Warm Cream           | `#FFF8F0` | Page backgrounds                                   |
| Background texture | —                    | —         | Subtle batik geometric pattern at very low opacity |

### Typography

- **Headings:** Zen Kaku Gothic (clean Japanese-inspired display font) with custom section markers using Jawi-inspired decorative strokes
- **Body:** Inter or Noto Sans — chosen for full multilingual support (EN, JP, ZH, MS)
- **Accents:** A decorative display font for section labels blending Khat (Jawi calligraphy) and Japanese brush stroke aesthetics

### Motifs & Decorative Elements

- **Batik / Songket patterns** — Used as section dividers, border accents, and background textures. Not literal illustrations, but geometric line-art overlays
- **Sakura petals** — Subtle floating particle animations on the hero section and as transition accents between content blocks
- **Wau Bulan (traditional Malay moon kite)** — Silhouette integrated into navigation or as a decorative motif. Iconic to Malaysia and visually striking
- **Noren (暖簾) curtain shapes** — The traditional split-curtain hung at Japanese shop and theater entrances. Used as section frame treatments, dividers, and content reveal transitions — a direct nod to AKB48's theater culture
- **Islamic geometric tile patterns** — Used for member profile card frames and content section backgrounds

---

## 2. Page Structure & Features

All features from the current official site ([klp48.my](https://klp48.my/)) are preserved but reorganized for better storytelling and user engagement.

### 2.1 Hero Section (Full-screen)

- Fullscreen **video background** (carried over from current site) framed within a decorative arch shape inspired by Malay/Islamic geometric archways merged with a noren (暖簾) split-curtain silhouette — evoking the entrance to a theater stage
- **Floating sakura petal** particle effect overlaying the video
- KLP48 logo centered with a tagline displayed in both **Bahasa Melayu** and **Japanese**
- **Language switcher** (EN / JP / ZH / MS) styled as elegant pill toggles instead of a dropdown

### 2.2 Announcement Ticker / Banner Strip

- Horizontal scrolling ribbon below the hero for urgent news (e.g., release postponements, official announcements)
- Bordered with a **gold songket-pattern** decorative edge
- Subtle jade-green glow on hover for each announcement item

### 2.3 News Section (`/news`)

- **Card-based layout** with date, category tag (News / Fanclub / Store), and title
- Cards feature a jade-green left border accent
- On hover: a **batik pattern overlay** fades in across the card background
- "See more" link styled as a Japanese-inspired arrow (`→`) with a trailing animated line
- Filterable by category

### 2.4 Member Section (`/member`) — _Centerpiece of the Redesign_

- Each member displayed in a custom **hexagonal or arch-shaped frame** inspired by Islamic geometric tiles
- **On hover:** Frame rotates slightly and reveals:
  - Member's name in both romanized and katakana/kanji form
  - Generation badge (Gen 1 / Gen 2)
  - A subtle color shift matching the member's personal accent color
- **Filter controls** for Gen 1 / Gen 2
- **Click-through** to a detailed profile page with:
  - Full bio and stats
  - Photo gallery
  - Social media links
  - Recent appearances and schedule

### 2.5 Release / Discography Section (`/discography`)

- Album covers displayed in a **horizontal carousel**
- **Vinyl record / CD-case reveal animation** on hover — the cover art slides to reveal track listing
- Each release shows: title, release date, and tracklist
- Background subtly **shifts color** to match the album art's dominant hue as the user scrolls through releases

### 2.6 Movie / Video Section (`/movie`)

- **YouTube embed grid** with custom-designed thumbnail overlays (jade-green play button icon)
- Section header uses the **batik geometric divider** motif
- Videos grouped by event or release

### 2.7 Schedule Section (`/schedule`)

- Clean **timeline or calendar view**
- Upcoming events marked with **jade-green pulsing dots**
- Past events shown in muted tones
- Events display: date, title, venue, and a "details" expandable panel

### 2.8 Fanclub Section (`/fanclub`)

- A premium-feeling **CTA card** with gold + jade gradient border
- Benefits listed with small illustrated icons (handshake, ticket, star, etc.)
- Clear **tier explanation** if applicable
- Direct sign-up / login integration
- Discord community link

### 2.9 About Section (`/about`)

- Narrative story of KLP48's founding and the AKB48 connection
- A **cultural fusion visual** — half batik pattern morphing into sakura pattern (illustrated SVG or animated CSS)
- Group photo with a **parallax scroll effect**
- Key milestones timeline (founding → debut → 1st concert → theater opening)

### 2.10 Footer

- **48 Group sister group logos** in a scrolling horizontal marquee (AKB48, SKE48, NMB48, HKT48, NGT48, STU48, JKT48, BNK48, MNL48, AKB48 Team SH, TPE48, CGM48)
- **Sponsor logos** section
- Legal links: FAQ, Privacy Policy, Terms of Service, Contact
- Social media links (X/Twitter, TikTok) styled as **circular jade-colored icons**
- Contact email
- Copyright notice

---

## 3. Navigation Design

### Desktop Navigation

- **Fixed top navbar** that transitions from transparent (over hero) to a frosted-glass jade-tinted bar on scroll
- Nav items: Home, News, Member, Release, Movie, About, Schedule, Fanclub
- Language switcher on the right side
- Social icons (X, TikTok) as small circular buttons

### Mobile Navigation

- **Hamburger menu** that opens a full-screen overlay with a batik-pattern background
- Large, thumb-friendly nav links
- Language switcher prominently placed at the top of the mobile menu

---

## 4. Interaction & Motion Design

| Element             | Animation                                                          |
| ------------------- | ------------------------------------------------------------------ |
| Page transitions    | Smooth cross-fade with brief batik-pattern wipe effect             |
| Scroll reveals      | Elements fade in with gentle upward drift (no aggressive parallax) |
| Button hover        | Ripple effect in jade green                                        |
| Link hover          | Underline animates in with sakura-pink stroke                      |
| Member cards        | Subtle 3D tilt on hover with frame rotation                        |
| Loading screen      | Minimal KLP48 logo with jade gem pulsing glow animation            |
| Hero                | Floating sakura petal particle system (CSS or canvas)              |
| Announcement ticker | Smooth horizontal auto-scroll with pause on hover                  |

---

## 5. Multilingual Support

KLP48 serves fans across four languages. The redesign handles this with:

- **Prominent language switcher** — visible on both desktop and mobile, styled as pill toggles (not hidden in a dropdown)
- **Supported languages:** English (EN), 日本語 (JP), 中文简体 (ZH), Bahasa Melayu (MS)
- **Font stack:** Noto Sans / Inter — full coverage for Latin, CJK, and Malay characters
- **RTL-ready structure** — although current languages are LTR, the layout should gracefully support Jawi script if ever needed
- **Localized content** — not just translated UI, but culturally adapted section copy where appropriate

---

## 6. What Makes This Different from the Current Site

The current [klp48.my](https://klp48.my/) uses a standard Peeeps platform template with minimal custom styling. It is functional but generic. This redesign addresses the following:

| Current Site                     | Redesigned Site                                                                 |
| -------------------------------- | ------------------------------------------------------------------------------- |
| Generic template layout          | Unique cultural identity — you _feel_ both Malaysia and Japan visually          |
| Members are a basic nav link     | Members are the emotional centerpiece with interactive profile cards            |
| Flat list of sections            | Narrative homepage flow: hero → news → members → music → events                 |
| No meaningful motion             | Sakura particles, batik reveals, and subtle scroll animations create atmosphere |
| Basic dropdown language switcher | Prominent, elegant pill-toggle language switcher                                |
| Standard footer                  | Rich footer with sister group marquee, sponsors, and social links               |
| No cultural visual identity      | Batik, songket, wau bulan, noren curtain, and sakura motifs throughout          |

---

## 7. Recommended Tech Stack

| Layer           | Technology               | Purpose                                                              |
| --------------- | ------------------------ | -------------------------------------------------------------------- |
| Backend         | Laravel 12               | Content management, fanclub auth, API routes, admin panel            |
| Frontend        | Vue 3 + TypeScript       | Component-based SPA with reactive UI                                 |
| Styling         | TailwindCSS v3 + PostCSS | Utility-first CSS with custom jade/sakura/gold theme config          |
| Build           | Vite                     | Fast HMR and optimized production builds                             |
| Database        | PostgreSQL               | Member data, news, discography, schedules, fanclub accounts          |
| Storage / CDN   | Supabase Storage         | Image and video hosting with built-in CDN and signed URLs            |
| Animation       | GSAP or Framer Motion    | Scroll-triggered animations, particle effects, page transitions      |
| Package Manager | npm                      | Dependency management                                                |
| Deployment      | TBD                      | Recommended: Docker + managed hosting (e.g., Laravel Forge, Railway) |

---

## 8. Media & Storage Strategy

### Overview

All media assets are hosted on **Supabase Storage**, which provides S3-compatible object storage with a built-in CDN edge network, Row-Level Security (RLS), and native PostgreSQL integration. Laravel's `Storage` facade connects to Supabase via the S3 driver with zero custom adapters.

### Storage Buckets

| Bucket          | Access Level          | Contents                                                                  |
| --------------- | --------------------- | ------------------------------------------------------------------------- |
| `public-assets` | Public                | Member photos, album art, logos, hero banners, batik/songket pattern SVGs |
| `videos`        | Public                | Promotional clips, concert teasers, embedded alongside YouTube            |
| `fanclub`       | Private (RLS)         | Paywalled videos, behind-the-scenes content, exclusive photo sets         |
| `cms`           | Public after approval | News article images, schedule banners, admin-uploaded media               |

### Public Content — YouTube Embeds

All publicly available music videos, concert footage, and promotional content should remain on **YouTube** and be embedded on the site via iframe or the YouTube IFrame API. Benefits:

- Free hosting, free CDN, free adaptive bitrate streaming
- Discoverability and SEO via YouTube search
- No bandwidth cost to the KLP48 infrastructure
- Existing KLP48 YouTube content stays in place

### Paywalled / Fanclub-Exclusive Content — Supabase Signed URLs

Private YouTube videos **cannot** be embedded — YouTube requires videos to be Public or Unlisted for iframe playback. Unlisted videos are not secure since anyone with the URL can watch and share it freely.

Instead, fanclub-exclusive video content uses the following flow:

1. Videos are uploaded to the **private `fanclub` bucket** in Supabase Storage (protected by RLS policies)
2. When an authenticated fanclub member navigates to a premium video page, the Vue frontend requests access from Laravel
3. Laravel verifies the user's **subscription status and tier** via middleware
4. If authorized, Laravel generates a **time-limited signed URL** (e.g., 2-hour expiry) using Supabase's `createSignedUrl()` via the S3 API
5. The signed URL is returned to the Vue frontend and fed into a video player component (Plyr or Video.js)
6. Once the signed URL expires, the link is dead — sharing it after expiry returns a 403

**Security notes:**

- Signed URLs prevent direct link sharing and hotlinking
- This does **not** prevent screen recording (only Widevine/FairPlay DRM can do that, which is far more complex and costly)
- For additional protection, consider watermarking fanclub videos with the viewer's member ID as a visible or invisible overlay — this deters redistribution
- All Supabase Storage buckets should have **CORS policies** locked to the KLP48 domain only

### Future Scaling Option

If video volume and bandwidth grow significantly, **Cloudflare R2** is a drop-in replacement for Supabase Storage:

- Zero egress bandwidth fees (critical for video-heavy sites)
- S3-compatible API — Laravel `Storage` config change is a one-liner
- Cloudflare's global CDN is automatic

For adaptive bitrate streaming (HLS) at scale, consider **Mux** or **Cloudflare Stream** as a dedicated video pipeline alongside Supabase for images.

---

## 9. Key Design References

- **Malaysian jade** — the official KLP48 brand color, symbolizing gentle aura, empathy, and fusion of values
- **Batik & Songket** — traditional Malay textile arts featuring intricate geometric and floral patterns
- **Wau Bulan** — the iconic Malaysian moon kite, a national cultural symbol
- **Sakura** — cherry blossom, the quintessential Japanese spring motif
- **Islamic geometric patterns** — tessellated tile art found in Malaysian mosques and architecture
- **Noren (暖簾)** — traditional Japanese split-curtain hung at theater and shop entrances, symbolizing the threshold to a performance space and directly referencing AKB48's theater tradition
- **AKB48 theater culture** — the intimate live performance tradition that defines the 48 Group identity

---

_Document prepared for the KLP48 website redesign project._
_All features from the current official site (klp48.my) are preserved and enhanced._

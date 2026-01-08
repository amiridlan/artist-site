KLP48 Website Redesign - Project Tasks
Project Overview
Project Name: KLP48 Official Website Redesign
Project Type: Frontend Web Application
Current Status: Sprint 0 Complete ✅
Next Phase: Sprint 1 - Core Navigation & Hero Section
Project Goal
Redesign the KLP48 official website (https://klp48.my) to improve user experience, visual appeal, and overall engagement. This is a frontend-only project - backend integration will come later.

Tech Stack
Core Technologies

Framework: Vue 3 (Composition API)
Build Tool: Vite
Language: TypeScript (Strict mode)
Styling: TailwindCSS v3 with PostCSS
State Management: Pinia
Routing: Vue Router
Package Manager: npm

Additional Libraries

Animation: GSAP, Framer Motion Vue (planned)
Carousel: Swiper.js
Date Handling: date-fns
Icons: Heroicons
Utilities: @vueuse/core

Future Backend (Not Current Focus)

Laravel 12
PostgreSQL
Supabase


Design Direction
Aesthetic: "Pop Editorial Maximalism"
A bold, high-energy design approach featuring:

Magazine-style layouts with Japanese street style influence
Dynamic grid-breaking compositions
Vibrant gradients with neon accents (#00ff9f, #b026ff, #ffed4e)
Heavy use of motion and parallax effects
Mix of kawaii elements with editorial sophistication
Bold typography (Outfit for display, DM Sans for body, Noto Sans JP for Japanese)

Design Principles

High Visual Impact: Every section should be memorable
Dynamic Hierarchy: Use size, color, spacing to guide attention
Smooth Animations: 60fps animations, scroll-triggered reveals
Responsive First: Mobile, tablet, desktop optimization
Accessibility: WCAG AA compliance, keyboard navigation, reduced motion support


Project Structure
klp48-redesign/
├── public/                   # Static assets
├── src/
│   ├── assets/              # Images, icons
│   ├── components/          # Vue components
│   │   ├── common/         # Reusable (Button, Card, Modal)
│   │   ├── layout/         # Header, Footer, Navigation
│   │   ├── home/           # Home page components
│   │   └── sections/       # Section components
│   ├── composables/         # Vue composables
│   ├── data/               # Mock JSON data
│   ├── layouts/            # Layout wrappers
│   ├── pages/              # Page components (8 pages)
│   ├── router/             # Vue Router config
│   ├── stores/             # Pinia stores
│   ├── styles/             # Global styles
│   ├── types/              # TypeScript types
│   └── utils/              # Helper functions
├── Configuration files...

Agile Sprint Plan
✅ Sprint 0: Project Setup (COMPLETE)
Duration: 3 days
Status: Complete
Completed:

 Initialize project structure
 Configure TailwindCSS with custom theme
 Set up TypeScript with strict mode
 Create type definitions (Member, News, Video, Release, Event)
 Create utility functions (helpers, constants)
 Set up Vue Router with 8 routes
 Create Pinia language store
 Configure ESLint + Prettier
 Create placeholder pages


🎯 Sprint 1: Core Navigation & Hero Section (CURRENT)
Duration: 2 weeks
Goal: Implement navigation and impactful hero section
User Stories

As a visitor, I want clear navigation so I can find content easily
As a visitor, I want to immediately understand what KLP48 is
As a mobile user, I want responsive navigation

Tasks to Complete
Header Component (src/components/layout/Header.vue)

 Create responsive header with logo
 Implement main navigation (NEWS, MEMBER, RELEASE, MOVIE, SCHEDULE, FANCLUB)
 Add language selector dropdown (EN, 日本語, 中文, Bahasa Melayu)
 Implement sticky header on scroll
 Add hamburger menu for mobile (<768px)
 Style with gradient background or glass morphism
 Add smooth transitions

Mobile Navigation (src/components/layout/MobileMenu.vue)

 Create slide-in mobile menu
 Implement overlay backdrop
 Add close button
 Include all navigation links
 Add language selector
 Animate menu items (staggered fade-in)
 Handle body scroll lock when open

Hero Section (src/components/home/HeroSection.vue)

 Create full-viewport hero section
 Add video background with fallback image
 Implement animated tagline with staggered text reveal
 Add primary CTA buttons (Join Fanclub, Watch Latest MV)
 Create scroll indicator animation
 Add parallax effect on scroll
 Ensure video loads efficiently (<2s)
 Make fully responsive

Footer Component (src/components/layout/Footer.vue)

 Create footer with sponsor logos
 Add social media links (Twitter, TikTok, Instagram, YouTube)
 Include quick links (FAQ, Privacy Policy, Terms, Contact)
 Add copyright notice
 Make responsive (stack on mobile)
 Style consistently with brand

Page Transitions

 Implement Vue Router page transitions
 Create fade/slide transition effects
 Ensure smooth 60fps animations
 Test on all routes

Home Page Layout (src/pages/Home.vue)

 Import and compose Hero, (empty section placeholders for now)
 Implement DefaultLayout wrapper
 Add smooth scroll behavior

Acceptance Criteria

 Navigation works on all screen sizes (mobile, tablet, desktop)
 Hero section loads in <2 seconds
 All animations run at 60fps
 Language selector changes active language (UI updates in future sprints)
 Header becomes sticky on scroll with transition
 Mobile menu opens/closes smoothly
 CTAs are clearly visible and clickable
 Footer displays all required information
 Page transitions work between all routes

Design Specs
Header

Height: 80px (desktop), 64px (mobile)
Background: Gradient or glass morphism with backdrop-blur
Logo: Left-aligned, ~120px width
Navigation: Center or right-aligned
Font: Outfit, weight 600
Active state: Underline or colored indicator

Hero Section

Height: 100vh (full viewport)
Video: MP4 format, optimized, autoplay, loop, muted
Tagline:

Font: Outfit, 72px (desktop), 36px (mobile)
Animation: Fade in + slide up, staggered by word
Color: Gradient text (primary-500 → accent-purple)


CTAs:

Primary button: Gradient background with hover scale
Secondary button: Outline style
Gap: 1rem between buttons



Footer

Background: dark-900 or gradient
Text color: white/gray-300
Sponsor logos: Grid layout, grayscale with hover color
Social icons: Hover scale + glow effect


📋 Sprint 2: News Section
Duration: 2 weeks
Goal: Create engaging news/content display system
Tasks (Summary - Detail When Starting Sprint)

 Create NewsCard component with hover effects
 Implement news grid layout (masonry-style)
 Add category filter tabs
 Create "Featured News" banner
 Build news detail page
 Implement infinite scroll or pagination
 Add loading skeletons

Mock Data Required
Create src/data/news.json with:

20+ news articles
Mix of categories (news, fanclub, store, event, release)
Sample images, dates, content
Featured flag for top stories


📋 Sprint 3: Member Section
Duration: 2 weeks
Goal: Showcase members with engaging profiles
Tasks (Summary)

 Create MemberCard with 3D hover effect
 Build member grid with filters
 Implement member detail modal/page
 Add search functionality
 Create "Featured Member" spotlight
 Implement sorting options

Mock Data Required
Create src/data/members.json with:

30+ member profiles
Names (English + Native)
Photos, positions, teams, generations
Birthdates, bios, social links
Status (active, graduated, on-hiatus)


📋 Sprint 4: Video & Release Sections
Duration: 2 weeks
Goal: Showcase music videos and releases
Tasks (Summary)

 Build video grid with thumbnails
 Integrate custom video player
 Create release showcase with carousel
 Add streaming platform links
 Implement filters and sorting
 Create countdown timer for upcoming releases

Mock Data Required

src/data/videos.json: 15+ videos with thumbnails, durations
src/data/releases.json: 10+ releases with covers, tracklists, streaming links


📋 Sprint 5: Schedule & Events
Duration: 2 weeks
Goal: Display upcoming events in calendar format
Tasks (Summary)

 Design calendar view component
 Build event cards
 Implement list/calendar toggle
 Add month navigation
 Create .ics export feature
 Filter by event type

Mock Data Required
Create src/data/events.json with:

20+ events (past, upcoming, ongoing)
Venues with addresses
Ticket info, dates, descriptions


📋 Sprint 6: Fan Club Section & Polish
Duration: 2 weeks
Goal: Create compelling fan club landing and overall polish
Tasks (Summary)

 Build fan club benefits section
 Create tier comparison table
 Add registration form (UI only)
 Performance optimizations (code splitting, lazy loading)
 Accessibility audit
 SEO optimization
 Cross-browser testing


📋 Sprint 7: Animation & Micro-interactions
Duration: 2 weeks
Goal: Add delightful animations and final polish
Tasks (Summary)

 Implement scroll-triggered animations (GSAP)
 Add parallax effects
 Create hover effects for all interactive elements
 Add page transition animations
 Implement custom cursor (desktop only)
 Add loading animations
 Optimize animation performance
 Add prefers-reduced-motion support


Important Implementation Rules
Code Standards

Always use TypeScript - No any types unless absolutely necessary
Follow Vue 3 Composition API - Use <script setup lang="ts">
Use TailwindCSS utilities - Avoid custom CSS unless necessary
Component naming: PascalCase for components, kebab-case for files
Props and emits: Always define with TypeScript
Comments: Add TODO comments for future backend integration

File Naming Conventions

Components: PascalCase (e.g., HeroSection.vue, NewsCard.vue)
Pages: PascalCase (e.g., Home.vue, Members.vue)
Utils: camelCase (e.g., helpers.ts, constants.ts)
Types: camelCase (e.g., member.ts, news.ts)

Git Commit Convention
feat: Add hero section with video background
fix: Resolve mobile menu scroll lock issue
style: Update header gradient colors
refactor: Optimize news card component
docs: Update README with sprint 2 progress
Component Structure Template
vue<template>
  <!-- Template here -->
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { PropType } from 'vue'

// Props
interface Props {
  title: string
  // Add props
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  click: [id: string]
}>()

// State
const isLoading = ref(false)

// Computed
const displayTitle = computed(() => props.title.toUpperCase())

// Methods
const handleClick = () => {
  emit('click', 'some-id')
}

// Lifecycle
onMounted(() => {
  // Initialize component
})
</script>

<style scoped>
/* Scoped styles if needed (prefer Tailwind) */
</style>

Mock Data Guidelines
Creating Mock Data Files
All mock data should be created in src/data/ directory as JSON files.
Example: src/data/news.json
json[
  {
    "id": "news-001",
    "title": "KLP48 Announces New Single Release",
    "slug": "klp48-announces-new-single-release",
    "category": "release",
    "excerpt": "Get ready for our exciting new single dropping next month!",
    "content": "Full article content here...",
    "thumbnail": "/images/news/news-001.jpg",
    "publishedAt": "2026-01-05T10:00:00Z",
    "featured": true,
    "tags": ["release", "music", "announcement"]
  }
]
Using Mock Data in Components
typescriptimport newsData from '@/data/news.json'
import type { News } from '@/types/news'

const news = ref<News[]>(newsData)
Image Assets for Mock Data

Use placeholder images from Unsplash or Pexels
Optimize images before adding to project
Store in public/images/ directory
Recommended sizes:

News thumbnails: 800x600px
Member photos: 600x800px
Video thumbnails: 1280x720px
Album covers: 1000x1000px




Performance Targets
Lighthouse Scores

Performance: >90
Accessibility: >95
Best Practices: >90
SEO: >90

Core Web Vitals

First Contentful Paint (FCP): <1.5s
Largest Contentful Paint (LCP): <2.5s
Time to Interactive (TTI): <3s
Cumulative Layout Shift (CLS): <0.1
First Input Delay (FID): <100ms

Optimization Strategies

Code Splitting: Lazy load routes and heavy components
Image Optimization: Use WebP format, lazy loading, responsive images
CSS Optimization: Purge unused Tailwind classes
JS Bundle: Keep main bundle <200KB gzipped
Caching: Implement service worker (future sprint)


Accessibility Requirements
WCAG 2.1 AA Compliance

 All images have alt text
 Color contrast ratio ≥4.5:1 for text
 Keyboard navigation works for all interactive elements
 Focus indicators visible on all focusable elements
 ARIA labels for icon-only buttons
 Skip to main content link
 Heading hierarchy (h1 → h2 → h3)
 Form inputs have labels
 Error messages are descriptive

Testing Checklist

 Test with keyboard only (Tab, Enter, Escape)
 Test with screen reader (NVDA or VoiceOver)
 Test with browser zoom (200%)
 Test with reduced motion enabled
 Run axe DevTools accessibility scan


Browser & Device Support
Browsers

Chrome/Edge: Last 2 versions ✅
Firefox: Last 2 versions ✅
Safari: Last 2 versions ✅
Mobile Safari (iOS): 13+ ✅
Chrome Android: Last 2 versions ✅

Devices to Test

Desktop: 1920x1080, 1366x768
Laptop: 1440x900
Tablet: iPad (1024x768), iPad Pro (1366x1024)
Mobile: iPhone 12 (390x844), Samsung Galaxy (360x740)

Responsive Breakpoints (Tailwind)
sm:  640px   // Small tablets
md:  768px   // Tablets
lg:  1024px  // Laptops
xl:  1280px  // Desktops
2xl: 1536px  // Large desktops

Development Workflow
Starting a New Sprint

Read sprint documentation in this file
Review design mockups (if provided)
Create mock data files in src/data/
Build components starting with smallest/simplest
Test responsiveness at each breakpoint
Test accessibility with keyboard and screen reader
Optimize performance (lazy loading, code splitting)
Update documentation with progress

Daily Development Checklist

 Pull latest changes from repository
 Review current sprint tasks
 Write component with TypeScript types
 Use TailwindCSS utilities (avoid custom CSS)
 Test component in isolation
 Test responsive behavior (mobile, tablet, desktop)
 Check browser console for errors
 Lint code (npm run lint)
 Format code (npm run format)
 Commit with conventional commit message
 Push to repository

Testing Before Sprint Completion

Functionality: All features work as expected
Responsiveness: Test on all breakpoints
Performance: Lighthouse score >90
Accessibility: No axe violations, keyboard navigation works
Browser Compatibility: Test on Chrome, Firefox, Safari
Code Quality: No ESLint errors, all types defined
Documentation: Update README with progress


File Locations Reference
Configuration Files (Root)
├── .env                    # Environment variables
├── .env.example           # Environment template
├── .eslintrc.cjs          # ESLint config
├── .gitignore             # Git ignore
├── .prettierrc            # Prettier config
├── index.html             # HTML entry
├── package.json           # Dependencies
├── postcss.config.js      # PostCSS config
├── README.md              # Project docs
├── tailwind.config.js     # Tailwind config
├── tsconfig.json          # TypeScript config
├── tsconfig.node.json     # TypeScript Node config
└── vite.config.ts         # Vite config
Source Files (src/)
src/
├── main.ts                # App entry point
├── App.vue                # Root component
├── assets/                # Static assets
├── components/            # Vue components
│   ├── common/           # Button, Card, Modal, etc.
│   ├── layout/           # Header, Footer, Navigation
│   ├── home/             # Home-specific components
│   └── sections/         # Reusable sections
├── composables/           # Vue composables
├── data/                  # Mock JSON data
├── layouts/               # Layout wrappers
├── pages/                 # Page components
├── router/                # Vue Router config
├── stores/                # Pinia stores
├── styles/                # Global CSS
├── types/                 # TypeScript types
└── utils/                 # Helper functions

Environment Variables
Development (.env)
bash# API Configuration (Future)
VITE_API_URL=http://localhost:8000/api
VITE_API_TIMEOUT=30000

# Feature Flags
VITE_ENABLE_ANALYTICS=false
VITE_ENABLE_DEVTOOLS=true

# CDN Configuration
VITE_CDN_URL=https://cdn.klp48.my

# Social Media Links
VITE_TWITTER_URL=https://x.com/KLP48official
VITE_TIKTOK_URL=https://x.gd/EutXI
VITE_INSTAGRAM_URL=
VITE_YOUTUBE_URL=

# App Configuration
VITE_APP_NAME=KLP48
VITE_APP_DESCRIPTION=Malaysia's Premier Idol Group
VITE_DEFAULT_LANG=en
Production (.env.production)
Update with production URLs when deploying.

Common Issues & Solutions
Issue: TailwindCSS not working
Solution: Ensure @tailwind directives are in src/styles/main.css and imported in main.ts
Issue: TypeScript errors on imports
Solution: Check tsconfig.json paths are configured correctly with @/* alias
Issue: Vue Router not found
Solution: Install vue-router: npm install vue-router
Issue: Images not loading
Solution: Use /images/... for public folder, or import from src/assets/
Issue: Animations janky/slow
Solution: Use will-change CSS property, check for layout thrashing, use transform instead of top/left
Issue: Mobile menu not closing
Solution: Add body scroll lock and properly handle click outside events

Resources & References
Official Documentation

Vue 3: https://vuejs.org/
TypeScript: https://www.typescriptlang.org/
TailwindCSS: https://tailwindcss.com/
Vite: https://vitejs.dev/
Pinia: https://pinia.vuejs.org/
Vue Router: https://router.vuejs.org/

Design Inspiration

Awwwards: https://www.awwwards.com/
Dribbble: https://dribbble.com/
Behance: https://www.behance.net/

Tools

Figma: Design tool
Chrome DevTools: Debugging
Vue DevTools: Vue debugging
Lighthouse: Performance testing
axe DevTools: Accessibility testing

Useful Libraries

@vueuse/core: Vue composables
GSAP: Advanced animations
Swiper: Touch sliders
date-fns: Date utilities
Heroicons: Icon library


Contact & Support
Project Lead

Review design decisions with project lead before implementing major changes
Ask for clarification on ambiguous requirements
Report blockers immediately

Getting Help

Check this TASK.md file first
Review Sprint documentation
Check component examples in codebase
Search official documentation
Ask for help if stuck >1 hour


Success Criteria
Sprint Completion
A sprint is complete when:

✅ All tasks marked as done
✅ Acceptance criteria met
✅ Code passes linting and type checking
✅ Responsive on all breakpoints
✅ Accessibility requirements met
✅ Performance targets achieved
✅ Cross-browser tested
✅ Documentation updated

Project Completion
Project is complete when:

✅ All 7 sprints finished
✅ All pages functional
✅ Mock data populated
✅ Animations polished
✅ Performance optimized
✅ Accessibility audited
✅ Ready for backend integration


Next Steps for AI Agent
Immediate Actions (Sprint 1)

Review this entire TASK.md file
Check that all Sprint 0 files are in correct locations
Install dependencies: npm install
Start dev server: npm run dev
Begin Sprint 1 tasks:

Start with Header component
Then Hero section
Then Footer
Then Mobile menu
Finally page transitions


Follow code standards defined above
Test frequently at each breakpoint
Commit regularly with conventional commit messages

Remember

This is frontend only - no backend integration yet
Use mock data from JSON files
Focus on visual appeal and UX
Performance and accessibility are priorities
Type safety with TypeScript always
Mobile-first responsive design


Current Sprint: Sprint 1 - Core Navigation & Hero Section
Status: Ready to Begin
Estimated Completion: 2 weeks
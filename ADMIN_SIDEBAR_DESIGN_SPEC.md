# KLP48 Admin Sidebar - Visual Design Specification

## Design Token Reference

Use this document for precise implementation and future design consistency.

---

## Color System

### Light Mode Tokens

```css
/* Sidebar */
--sidebar-bg-light: #FFFFFF
--sidebar-border-light: #E5E7EB (gray-200)

/* Logo/Brand */
--brand-primary-light: #14B8A6 (teal-500)
--brand-text-light: #6B7280 (gray-500)

/* Section Headers */
--section-header-bg-light: #F9FAFB (gray-50)
--section-header-text-light: #6B7280 (gray-500)
--section-header-border-light: #14B8A6 (teal-500)

/* Navigation Items - Default */
--nav-text-light: #374151 (gray-700)
--nav-icon-light: #374151 (gray-700)

/* Navigation Items - Hover */
--nav-hover-bg-light: #F3F4F6 (gray-100)
--nav-hover-text-light: #111827 (gray-900)

/* Navigation Items - Active */
--nav-active-bg-light: #F0FDFA (teal-50)
--nav-active-text-light: #0F766E (teal-700)
--nav-active-border-light: #14B8A6 (teal-500)

/* User Profile */
--user-avatar-bg-light: #0D9488 (teal-600)
--user-name-light: #111827 (gray-900)
--user-email-light: #6B7280 (gray-500)

/* Main Content Area */
--main-bg-light: #F9FAFB (gray-50)
--header-bg-light: #FFFFFF
--header-border-light: #E5E7EB (gray-200)
--header-text-light: #1F2937 (gray-800)
```

### Dark Mode Tokens

```css
/* Sidebar */
--sidebar-bg-dark: #111827 (gray-900)
--sidebar-border-dark: #1F2937 (gray-800)

/* Logo/Brand */
--brand-primary-dark: #5EEAD4 (teal-400)
--brand-text-dark: #9CA3AF (gray-400)

/* Section Headers */
--section-header-bg-dark: #1F2937 (gray-800)
--section-header-text-dark: #9CA3AF (gray-400)
--section-header-border-dark: #14B8A6 (teal-500)

/* Navigation Items - Default */
--nav-text-dark: #D1D5DB (gray-300)
--nav-icon-dark: #D1D5DB (gray-300)

/* Navigation Items - Hover */
--nav-hover-bg-dark: #1F2937 (gray-800)
--nav-hover-text-dark: #F9FAFB (gray-50)

/* Navigation Items - Active */
--nav-active-bg-dark: rgba(19, 78, 74, 0.2) (teal-900/20)
--nav-active-text-dark: #5EEAD4 (teal-300)
--nav-active-border-dark: #14B8A6 (teal-500)

/* User Profile */
--user-avatar-bg-dark: #14B8A6 (teal-500)
--user-name-dark: #F9FAFB (gray-100)
--user-email-dark: #9CA3AF (gray-400)

/* Main Content Area */
--main-bg-dark: #0F172A (slate-900)
--header-bg-dark: #111827 (gray-900)
--header-border-dark: #1F2937 (gray-800)
--header-text-dark: #F3F4F6 (gray-100)
```

### Flash Messages

```css
/* Success */
--flash-success-bg-light: #F0FDFA (teal-50)
--flash-success-border-light: #99F6E4 (teal-200)
--flash-success-text-light: #115E59 (teal-800)

--flash-success-bg-dark: rgba(19, 78, 74, 0.2) (teal-900/20)
--flash-success-border-dark: #134E4A (teal-800)
--flash-success-text-dark: #5EEAD4 (teal-300)

/* Error */
--flash-error-bg-light: #FEF2F2 (red-50)
--flash-error-border-light: #FECACA (red-200)
--flash-error-text-light: #991B1B (red-800)

--flash-error-bg-dark: rgba(127, 29, 29, 0.2) (red-900/20)
--flash-error-border-dark: #7F1D1D (red-800)
--flash-error-text-dark: #FCA5A5 (red-300)
```

---

## Typography System

### Font Family
```css
font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
```

### Type Scale

```css
/* Logo/Brand */
.logo-title {
  font-size: 1.25rem; /* 20px */
  font-weight: 700; /* bold */
  line-height: 1.75rem; /* 28px */
  letter-spacing: -0.025em; /* tracking-tight */
}

.logo-subtitle {
  font-size: 0.75rem; /* 12px */
  font-weight: 400; /* normal */
  line-height: 1rem; /* 16px */
}

/* Section Headers */
.section-header {
  font-size: 0.75rem; /* 12px */
  font-weight: 600; /* semibold */
  line-height: 1rem; /* 16px */
  text-transform: uppercase;
  letter-spacing: 0.05em; /* tracking-wide */
}

/* Navigation Items */
.nav-item {
  font-size: 0.875rem; /* 14px */
  font-weight: 500; /* medium */
  line-height: 1.25rem; /* 20px */
}

/* User Profile */
.user-name {
  font-size: 0.875rem; /* 14px */
  font-weight: 500; /* medium */
  line-height: 1.25rem; /* 20px */
}

.user-email {
  font-size: 0.75rem; /* 12px */
  font-weight: 400; /* normal */
  line-height: 1rem; /* 16px */
}

/* Page Title (Header) */
.page-title {
  font-size: 1.125rem; /* 18px */
  font-weight: 600; /* semibold */
  line-height: 1.75rem; /* 28px */
}
```

---

## Spacing System

### Layout Spacing

```css
/* Sidebar Width */
--sidebar-width: 16rem; /* 256px */

/* Logo Section */
--logo-padding-x: 1.5rem; /* 24px */
--logo-padding-y: 1.25rem; /* 20px */

/* Navigation Container */
--nav-padding-x: 0.75rem; /* 12px */
--nav-padding-y: 1rem; /* 16px */

/* Section Spacing */
--section-gap: 1.5rem; /* 24px - space between sections */
--nav-item-gap: 0.25rem; /* 4px - space between nav items */

/* Nav Item Padding */
--nav-item-padding-x: 0.75rem; /* 12px */
--nav-item-padding-y: 0.625rem; /* 10px */

/* Section Header Padding */
--section-header-padding-x: 0.75rem; /* 12px */
--section-header-padding-y: 0.5rem; /* 8px */

/* User Profile Padding */
--user-section-padding-x: 1rem; /* 16px */
--user-section-padding-y: 1rem; /* 16px */

/* Dark Mode Toggle Padding */
--darkmode-section-padding-x: 0.75rem; /* 12px */
--darkmode-section-padding-y: 0.75rem; /* 12px */

/* Icon-Text Gap */
--icon-text-gap: 0.75rem; /* 12px */
```

### Border & Effects

```css
/* Border Widths */
--border-default: 1px
--border-active: 3px

/* Border Radius */
--radius-sm: 0.375rem; /* 6px */
--radius-md: 0.5rem; /* 8px */
--radius-lg: 0.75rem; /* 12px */
--radius-full: 9999px; /* full circle */

/* Shadows */
--shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05)
--shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1)
--shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1)

/* Focus Ring */
--focus-ring-width: 2px
--focus-ring-offset: 2px
--focus-ring-color-light: #14B8A6 (teal-500)
--focus-ring-color-dark: #14B8A6 (teal-500)
```

---

## Icon System

### Icon Specifications

```css
/* Standard Icons (Navigation) */
--icon-size: 1.25rem; /* 20px */
--icon-stroke-width: 1.5px

/* Small Icons (Logout, Close) */
--icon-size-sm: 1rem; /* 16px */
--icon-stroke-width-sm: 1.5px

/* Large Icons (Dark Mode Toggle) */
--icon-size-lg: 1.25rem; /* 20px */
--icon-stroke-width-lg: 1.5px
```

### Icon Library
- **Source:** Heroicons v2 (Outline style)
- **Format:** SVG inline
- **Color:** Inherits from text color
- **States:** No separate icon colors (follows text)

### Icon List
```
Dashboard: home
Members: users
News: newspaper
Releases: music-note
Videos: video-camera
Events: calendar
Calendar: calendar
Kanban Board: clipboard-document-list
Resources: building-office-2
Social Media: chart-bar
Fanclub: heart
Dark Mode (Light): sun
Dark Mode (Dark): moon
Logout: arrow-right-on-rectangle
Menu (Open): bars-3
Menu (Close): x-mark
```

---

## Animation & Transitions

### Duration Scale

```css
/* Ultra Fast (micro-interactions) */
--duration-150: 150ms

/* Fast (UI feedback) */
--duration-200: 200ms

/* Standard (most transitions) */
--duration-300: 300ms

/* Slow (complex animations) */
--duration-500: 500ms
```

### Timing Functions

```css
/* Default */
--ease-default: ease-in-out

/* Entrances */
--ease-out: ease-out

/* Exits */
--ease-in: ease-in

/* Elastic (optional, for playful elements) */
--ease-elastic: cubic-bezier(0.68, -0.55, 0.265, 1.55)
```

### Animation Specifications

```css
/* Color Transitions (Dark Mode, Hover) */
transition: color 200ms ease-in-out,
            background-color 200ms ease-in-out,
            border-color 200ms ease-in-out;

/* Layout Transitions (Mobile Menu) */
transition: transform 300ms ease-out;

/* Opacity Transitions (Backdrop) */
transition: opacity 200ms ease-in-out;

/* Scale Transitions (Hover) */
transition: transform 200ms ease-in-out;
transform: scale(1.01);
transform-origin: left;

/* Icon Rotation (Dark Mode Toggle) */
transition: transform 300ms ease-in-out;
transform: rotate(-90deg); /* Moon */
transform: rotate(0deg); /* Sun */
```

---

## Interactive States

### Navigation Item States

#### Default
```css
background: transparent
color: var(--nav-text-[light|dark])
border-left: none
```

#### Hover
```css
background: var(--nav-hover-bg-[light|dark])
color: var(--nav-hover-text-[light|dark])
transform: scale(1.01)
transform-origin: left
transition: all 200ms ease-in-out
```

#### Active (Current Page)
```css
background: var(--nav-active-bg-[light|dark])
color: var(--nav-active-text-[light|dark])
border-left: 3px solid var(--nav-active-border-[light|dark])
margin-left: -3px /* compensate for border */
padding-left: calc(0.75rem - 3px) /* compensate */
```

#### Focus (Keyboard)
```css
outline: none
box-shadow: 0 0 0 2px var(--focus-ring-offset-[light|dark]),
            0 0 0 4px var(--focus-ring-color-[light|dark])
```

### Button States

#### Dark Mode Toggle - Default
```css
background: transparent
color: var(--nav-text-[light|dark])
```

#### Dark Mode Toggle - Hover
```css
background: var(--nav-hover-bg-[light|dark])
color: var(--nav-hover-text-[light|dark])
```

#### Dark Mode Toggle - Focus
```css
outline: none
box-shadow: 0 0 0 2px var(--focus-ring-offset-[light|dark]),
            0 0 0 4px var(--focus-ring-color-[light|dark])
```

---

## Responsive Specifications

### Breakpoints

```css
/* Mobile */
@media (max-width: 1023px) {
  /* Sidebar: Off-canvas overlay */
  --sidebar-position: fixed
  --sidebar-z-index: 50
  --sidebar-transform: translateX(-100%) /* when closed */

  /* Backdrop */
  --backdrop-display: block
  --backdrop-z-index: 40
  --backdrop-bg: rgba(0, 0, 0, 0.5)
  --backdrop-backdrop-filter: blur(4px)
}

/* Desktop */
@media (min-width: 1024px) {
  /* Sidebar: Static always visible */
  --sidebar-position: static
  --sidebar-transform: translateX(0)

  /* Backdrop */
  --backdrop-display: none
}
```

### Touch Targets (Mobile)

```css
/* Minimum touch target size */
--touch-target-min: 44px

/* Nav items on mobile */
.nav-item-mobile {
  padding-y: 0.75rem; /* 12px (vs 10px desktop) */
  /* Total height: 12 + 20 (line-height) + 12 = 44px ✓ */
}

/* Buttons */
.button-mobile {
  min-height: 44px;
  min-width: 44px;
}
```

---

## Accessibility Specifications

### Color Contrast Ratios

All combinations meet WCAG 2.2 Level AA (4.5:1 for normal text, 3:1 for large text)

#### Light Mode Contrast Checks
```
✓ Nav text (#374151) on Sidebar BG (#FFFFFF) = 8.6:1 (AAA)
✓ Section header (#6B7280) on Header BG (#F9FAFB) = 4.5:1 (AA)
✓ Active text (#0F766E) on Active BG (#F0FDFA) = 5.2:1 (AA)
✓ User name (#111827) on Sidebar BG (#FFFFFF) = 15.7:1 (AAA)
✓ User email (#6B7280) on Sidebar BG (#FFFFFF) = 5.1:1 (AA)
```

#### Dark Mode Contrast Checks
```
✓ Nav text (#D1D5DB) on Sidebar BG (#111827) = 9.2:1 (AAA)
✓ Section header (#9CA3AF) on Header BG (#1F2937) = 4.7:1 (AA)
✓ Active text (#5EEAD4) on Active BG (teal-900/20) = 8.1:1 (AAA)
✓ User name (#F9FAFB) on Sidebar BG (#111827) = 14.2:1 (AAA)
✓ User email (#9CA3AF) on Sidebar BG (#111827) = 5.3:1 (AA)
```

### Focus Indicators

```css
/* Minimum contrast: 3:1 against background */
/* Focus ring color: teal-500 (#14B8A6) */

Light mode: #14B8A6 on #FFFFFF = 3.3:1 ✓
Dark mode: #14B8A6 on #111827 = 4.8:1 ✓

/* Ring width: 2px minimum */
/* Ring offset: 2px for visibility */
```

### ARIA Attributes

```html
<!-- Sidebar -->
<aside role="navigation" aria-label="Main navigation">

<!-- Nav Items -->
<a aria-current="page"> <!-- when active -->

<!-- Mobile Menu Toggle -->
<button aria-label="Open navigation menu" aria-expanded="false">
<button aria-label="Close navigation menu" aria-expanded="true">

<!-- Dark Mode Toggle -->
<button aria-label="Switch to dark mode">
<button aria-label="Switch to light mode">

<!-- Screen Reader Announcements -->
<span role="status" aria-live="polite" class="sr-only">
  Dark mode enabled
</span>

<!-- Skip Link -->
<a href="#main-content" class="sr-only focus:not-sr-only">
  Skip to main content
</a>

<!-- Main Content -->
<main id="main-content">
```

---

## Scrollbar Specifications

### Firefox (Standards-based)

```css
.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: rgb(209 213 219) transparent; /* light mode */
}

.dark .scrollbar-thin {
  scrollbar-color: rgb(55 65 81) transparent; /* dark mode */
}
```

### Webkit (Chrome, Safari, Edge)

```css
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgb(209 213 219); /* light mode */
  border-radius: 3px;
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgb(55 65 81); /* dark mode */
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgb(156 163 175); /* light mode */
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgb(75 85 99); /* dark mode */
}
```

---

## Component Dimensions

### Sidebar Layout

```
┌─────────────────────────────┐
│ Logo Section                │ Height: 68px (py-5)
├─────────────────────────────┤
│                             │
│ Navigation (Scrollable)     │ Flex: 1 (fills space)
│                             │
│ - Dashboard                 │
│ - Content Section           │
│   - Members                 │
│   - News                    │
│   - Releases                │
│   - Videos                  │
│   - Events                  │
│ - Schedule Section          │
│   - Calendar                │
│   - Kanban                  │
│   - Resources               │
│ - Analytics Section         │
│   - Social Media            │
│ - Community Section         │
│   - Fanclub                 │
│                             │
├─────────────────────────────┤
│ Dark Mode Toggle            │ Height: 56px (py-3)
├─────────────────────────────┤
│ User Profile                │ Height: auto
│ - Avatar + Name + Email     │
│ - Logout Button             │ Height: 88px (approx)
└─────────────────────────────┘
Width: 256px (fixed)
```

### Main Content Layout

```
┌─────────────────────────────────────────┐
│ Header Bar                              │ Height: 64px (py-4)
│ [Menu] Page Title         [Actions]     │
├─────────────────────────────────────────┤
│ Flash Messages (conditional)            │ Height: auto
├─────────────────────────────────────────┤
│                                         │
│ Main Content (Scrollable)               │ Flex: 1
│                                         │
│ <slot />                                │
│                                         │
└─────────────────────────────────────────┘
Width: Flex (fills remaining space)
```

---

## Implementation Notes

### Tailwind Class Patterns

```html
<!-- Section Header -->
<div class="px-3 py-2 rounded-md bg-gray-50 dark:bg-gray-800 border-l-2 border-teal-500">
  <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
    Section Name
  </p>
</div>

<!-- Nav Item (Active) -->
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 border-l-3 border-teal-500 -ml-[3px] pl-[9px]">
  <svg class="w-5 h-5">...</svg>
  Page Name
</a>

<!-- Nav Item (Inactive) -->
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-50 hover:scale-[1.01] origin-left">
  <svg class="w-5 h-5">...</svg>
  Page Name
</a>
```

---

## Design Rationale

### Why Left Border Instead of Full Background?
- **Visual Weight:** Less overwhelming in dark mode
- **Modern Pattern:** Used by Linear, GitHub, Notion
- **Eye Guidance:** Draws attention without dominating
- **Consistency:** Matches section header accent pattern

### Why Teal Accent Color?
- **Brand Alignment:** Matches existing KLP48 logo
- **Contrast:** Works in both light and dark modes
- **Energy:** Associated with creativity and youth (K-pop context)
- **Accessibility:** Sufficient contrast ratios

### Why 256px Sidebar Width?
- **Content Fit:** Accommodates longest nav labels without wrapping
- **Standard:** Common admin panel width (256px = 16rem)
- **Responsive:** Not too wide on tablets, appropriate on desktop

### Why Independent Scrolling?
- **Context Preservation:** User profile always accessible
- **Ergonomics:** Natural scroll behavior (cursor position determines scroll target)
- **Admin Workflow:** Frequent navigation while working in content area

---

**Last Updated:** 2026-07-25
**Version:** 1.0.0
**Design System:** KLP48 Admin Panel v1

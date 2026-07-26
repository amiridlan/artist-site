# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Full-stack website for KLP48 (Malaysia's AKB48 sister group) with:
- **Frontend** (`/frontend`): Vue 3 SPA for public website
- **Backend** (`/backend`): Laravel 13 API + Vue/Inertia admin panel

## Development Commands

### Backend (Laravel)
```bash
cd backend
composer install                  # Install PHP dependencies
npm install                       # Install JS dependencies for admin
composer run dev                  # Start all services (server, queue, logs, vite)
php artisan migrate --seed        # Run migrations with seeders
php artisan storage:link          # Create public storage symlink
composer test                     # Run PHPUnit tests
```

### Frontend (Vue)
```bash
cd frontend
npm install                       # Install dependencies
npm run dev                       # Start Vite dev server (port 3000)
npm run build                     # Production build (vue-tsc + vite)
npm run lint                      # ESLint with auto-fix
npm run format                    # Prettier formatting
```

### Running Both
Terminal 1: `cd backend && composer run dev` (runs Laravel + queue + logs + admin Vite)
Terminal 2: `cd frontend && npm run dev` (runs frontend Vite on port 3000)

## Architecture

### Frontend-Backend Communication
- Frontend uses native `fetch` via `useApi` composable (`frontend/src/composables/useApi.ts`)
- API base URL: `VITE_API_URL` env var, defaults to `http://localhost:8000/api`
- Vite proxies `/api` requests to backend in dev mode
- All API responses use camelCase JSON; `?lang=ms|ja` query param for translations

### Multi-Language Support
- **Frontend**: vue-i18n with 3 locales (EN/MS/JA), persisted to localStorage (`klp48-lang`)
- **Backend**: Polymorphic `translations` table via `HasTranslations` trait
- API endpoints accept `?lang=` param; English is default fallback

### Authentication (Dual System)
- **Admin Panel**: Laravel session auth at `/admin/login`
- **Fan Club API**: Laravel Sanctum tokens (`/api/fan/*` endpoints)

### State Management
- Pinia stores in `frontend/src/stores/`: `language`, `fan`, `navigation`
- Fan auth state synced with Sanctum token in localStorage

### Key Directories
| Path | Purpose |
|------|---------|
| `frontend/src/pages/` | Route components (lazy-loaded) |
| `frontend/src/composables/` | `useApi`, `useGsap` hooks |
| `frontend/src/types/` | TypeScript interfaces for API data |
| `backend/app/Http/Controllers/Api/` | Public JSON API |
| `backend/app/Http/Controllers/Admin/` | Inertia admin CRUD |
| `backend/app/Http/Resources/` | API JSON transformers |
| `backend/app/Models/Concerns/` | HasTranslations trait |

### File Storage
- Development: Local `public` disk (`storage/app/public/` → `public/storage/`)
- Production: Cloudflare R2 (`MEDIA_DISK=r2` in `.env`)

## API Endpoints

| Endpoint | Description |
|----------|-------------|
| `GET /api/members` | List members (filterable by generation/status) |
| `GET /api/members/{slug}` | Single member by slug |
| `GET /api/news` | News articles |
| `GET /api/news/{slug}` | Single article |
| `GET /api/releases` | Music releases |
| `GET /api/videos` | YouTube videos |
| `GET /api/events` | Events/concerts |
| `POST /api/fan/login` | Fan authentication |

## Calendar & Kanban System

### Overview
Comprehensive schedule management system with RBAC, conflict detection, and kanban workflow.

### Features
- **Unified Calendar**: 7 event types (performance, appearance, filming, practice, day-off, staff, social media)
- **Kanban Board**: 4-stage workflow (backlog → planning → confirmed → completed)
- **Conflict Detection**: Automatic detection of artist double-booking, day-off conflicts, staff/resource availability
- **Role-Based Access**: 4 roles with granular permissions (Super Admin, Marketing Dept, Events Dept, Artist)
- **Cross-Visibility**: All users see all calendars, but edit permissions are department-specific

### Database Tables
- `schedule_events`: All schedule events with polymorphic relationships
- `kanban_cards`: Kanban workflow cards
- `resources`: Venues, equipment, vehicles
- `conflict_logs`: Audit trail of scheduling conflicts
- `model_has_roles`, `model_has_permissions`: Spatie Permission RBAC
- Pivot tables: `member_schedule_event`, `schedule_event_user`, `kanban_card_member`, etc.

### User Roles & Permissions

**Super Admin** (`admin@klp48.com` / `password`)
- Full access to everything
- Can override conflicts
- View all conflict logs

**Marketing Department** (`marketing1@klp48.com`, `marketing2@klp48.com` / `password`)
- Full CRUD: Social media posts, content filming, practice days
- View-only: Performances, appearances, day-offs, staff events
- Access: Kanban board, conflict logs

**Events Department** (`events1@klp48.com`, `events2@klp48.com` / `password`)
- Full CRUD: Performances, appearances, staff events
- View-only: Social media, content, practice, day-offs
- Access: Kanban board, resources, conflict logs

**Artist** (`yishyan@klp48.com`, `tiffany@klp48.com`, `salwa@klp48.com` / `password`)
- View: Own schedule only (filtered automatically)
- Full CRUD: Own day-offs
- No access: Kanban board, resources

### Admin Routes

| Route | Description |
|-------|-------------|
| `GET /admin/calendar` | FullCalendar view with filters |
| `GET /admin/calendar/events` | JSON endpoint for FullCalendar |
| `GET /admin/schedule-events` | List view with pagination |
| `GET /admin/schedule-events/create?type=X` | Create event form |
| `POST /admin/schedule-events` | Store event (with conflict check) |
| `GET /admin/schedule-events/{id}/edit` | Edit event form |
| `PUT /admin/schedule-events/{id}` | Update event (with conflict check) |
| `DELETE /admin/schedule-events/{id}` | Delete event |
| `GET /admin/kanban` | Kanban board with drag-drop |
| `POST /admin/kanban` | Create kanban card |
| `PATCH /admin/kanban/{id}/move` | Move card between stages |
| `POST /admin/kanban/{id}/confirm` | Confirm card → create schedule event |
| `GET /admin/resources` | List resources |
| `GET /admin/conflict-logs` | View conflict audit trail |

### Key Components

**Backend Services**
- `App\Services\ConflictDetectionService`: Centralized conflict detection logic
  - `checkScheduleEventConflicts()`: Orchestrates all checks
  - `checkArtistDoubleBooking()`: Detects overlapping artist schedules
  - `checkArtistDayOffConflict()`: Checks day-off violations
  - `checkStaffAvailability()`: Staff scheduling conflicts
  - `checkResourceAvailability()`: Resource booking conflicts

**Backend Models** (all in `app/Models/`)
- `ScheduleEvent`: With scopes `confirmed()`, `forMember()`, `forStaff()`, `overlapping()`
- `KanbanCard`: With scope `inStage()`
- `Resource`: With scope `active()`
- `ConflictLog`: Polymorphic conflict tracking

**Frontend Components** (all in `resources/js/Pages/Admin/`)
- `Calendar/Index.vue`: FullCalendar integration
- `ScheduleEvents/Index.vue`: List view with filters
- `ScheduleEvents/Create.vue`: Event creation with conflict warnings
- `ScheduleEvents/Edit.vue`: Event editing with conflict display
- `Kanban/Index.vue`: Drag-drop kanban board
- `Resources/Index.vue`, `Create.vue`, `Edit.vue`: Resource management
- `Components/Admin/ConflictWarning.vue`: Reusable conflict alert
- `Components/Admin/ConfirmKanbanModal.vue`: Kanban → Calendar confirmation

### Workflow Examples

**Creating a Performance Event**
1. User navigates to Schedule Events → "New Event" → "Artist Performance"
2. Fills form: title, date/time, venue, members, staff, resources
3. On submit, `ConflictDetectionService` checks for conflicts
4. If conflicts found: Show warning with details
5. Super Admin can override; others must resolve
6. Event saved with conflict notes if overridden

**Kanban to Calendar**
1. Create card in "Backlog" stage
2. Drag to "Planning" stage (assign details)
3. Click "Confirm" button
4. Fill schedule details modal (date, time, venue, resources)
5. Conflict check runs automatically
6. On confirm: Card moves to "Confirmed", schedule event created

**Conflict Detection**
- Runs on: Event create, event update, kanban confirm
- Checks: Artist double-booking, day-off violations, staff conflicts, resource conflicts
- Severity levels: `error` (blocks save), `warning` (allows with override)
- All conflicts logged to `conflict_logs` table

### Demo Data

**9 Resources**
- 3 Venues: KL Live Hall, Studio A, Outdoor Space
- 3 Equipment: Camera, Microphones, Stage Lights
- 3 Vehicles: Tour Bus, Equipment Van, Staff MPV

**7 Schedule Events** (includes 2 conflict scenarios)
- Event 1: Summer Concert (next week)
- Event 2: Music Video Shoot (tomorrow 9am-5pm)
- Event 3: Magazine Photo Shoot (tomorrow 2pm-6pm) ⚠️ **CONFLICTS with Event 2** (Yi Shyan double-booked)
- Event 4: Tiffany Day-Off (day after tomorrow)
- Event 5: Dance Practice (day after tomorrow 10am-4pm) ⚠️ **CONFLICTS with Event 4** (Tiffany on day-off)
- Event 6: Instagram Live Stream (next month)
- Event 7: Monthly Planning Meeting (3 days from now)

**6 Kanban Cards**
- 2 in Backlog: Autumn Fan Meeting, Behind-the-Scenes Vlog
- 2 in Planning: Radio Interview, TikTok Dance Challenge
- 1 in Confirmed: Summer Concert
- 1 in Completed: Spring Showcase

### NPM Dependencies (Calendar/Kanban)
```bash
@fullcalendar/vue3
@fullcalendar/core
@fullcalendar/daygrid
@fullcalendar/timegrid
@fullcalendar/interaction
@fullcalendar/list
vue-draggable-plus
```

### Composer Dependencies (RBAC)
```bash
spatie/laravel-permission
```

## Admin Panel UI/UX Features

### Sidebar Layout (v2.0 - 2026-07-26)

Modern, compact sidebar design with enhanced user experience:

**Key Features:**
- **Independent Height**: Sidebar uses `h-screen` + `sticky` positioning, not coupled to dashboard content height
- **User Dropdown Menu**: Click user section to access dark mode toggle and logout
- **Compact Navigation**: 33% more items visible without scrolling (reduced spacing)
- **Scroll Isolation**: Navigation and dashboard scroll independently (CSS `overscroll-behavior: contain`)
- **Full Accessibility**: WCAG 2.2 AA compliant with keyboard navigation and screen reader support

**User Menu:**
- Opens upward above user info
- Contains: Dark/Light mode toggle (amber sun ☀️ / indigo moon 🌙) + "Log Out" button
- Click outside or press Escape to close
- Smooth slide-up animation (200ms)

**Navigation Spacing:**
- Container: `space-y-3` (12px) instead of `space-y-6` (24px) - **50% reduction**
- Items: `space-y-0.5` (2px) instead of `space-y-1` (4px) - **50% reduction**
- Headers: `py-1.5` (6px) instead of `py-2` (8px) - **25% reduction**

**Files:**
- `backend/resources/js/Layouts/AdminLayout.vue` - Main implementation
- `backend/resources/js/Components/Admin/NavItem.vue` - Nav item component
- `ADMIN_SIDEBAR_UX_IMPROVEMENTS_2026.md` - Full documentation

### Date & Time Components

Enhanced date/time pickers with comprehensive improvements:

**Components:**
- `DateInput.vue`: Standalone date picker with timezone handling
- `TimeInput.vue`: Standalone time picker with validation
- `DateTimeInput.vue`: Combined date+time picker (legacy, still available)

**Features:**
- Timezone-aware data handling (prevents UTC conversion bugs)
- Quick select shortcuts (Today, Tomorrow, Now, +1hr, etc.)
- Dark mode support with theme-aware colors
- Loading and disabled states
- Inline validation with error messages
- Min/max date constraints
- Helper text support
- WCAG 2.2 AA accessible

**Usage Pattern:**
```vue
<!-- Separate inputs for better UX -->
<DateInput v-model="form.start_date" :minDate="new Date()" required />
<TimeInput v-model="form.start_time" required />

<!-- Watchers combine for backend -->
watch([() => form.start_date, () => form.start_time], () => {
  if (form.start_date && form.start_time) {
    form.start_datetime = `${form.start_date}T${form.start_time}`
  }
})
```

**Files:**
- `backend/resources/js/Components/Admin/DateInput.vue`
- `backend/resources/js/Components/Admin/TimeInput.vue`
- `backend/resources/js/Components/Admin/DateTimeInput.vue`
- `backend/DATE_PICKER_IMPROVEMENTS_SUMMARY.md` - Component documentation

### Dark Mode

Comprehensive dark mode implementation across all admin pages:

**Features:**
- System preference detection on first load
- Manual toggle via user dropdown menu
- Persisted to localStorage (`dark-mode` key)
- Smooth transitions (300ms) with reduced motion support
- Theme-aware components and icons

**Implementation:**
- `backend/resources/js/composables/useDarkMode.js` - Core composable
- Uses Tailwind's `dark:` variant classes
- HTML `<html class="dark">` toggle pattern
- All admin components support both themes

## Testing

```bash
# Backend PHPUnit tests (uses SQLite in-memory)
cd backend && composer test

# Frontend type checking
cd frontend && npx vue-tsc --noEmit
```

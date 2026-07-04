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

## Testing

```bash
# Backend PHPUnit tests (uses SQLite in-memory)
cd backend && composer test

# Frontend type checking
cd frontend && npx vue-tsc --noEmit
```

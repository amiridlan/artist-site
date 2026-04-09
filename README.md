# KLP48 Official Website

Full-stack website for KLP48 — Malaysia's AKB48 sister group — consisting of a Vue 3 frontend and a Laravel backend with a custom Vue + Inertia admin panel.

---

## Tech Stack

### Frontend (`/frontend`)
| Layer | Technology |
|---|---|
| Framework | Vue 3 (Composition API) |
| Build Tool | Vite |
| Language | TypeScript |
| Styling | TailwindCSS v4 |
| Routing | Vue Router |
| i18n | vue-i18n (EN / MS / JA) |
| HTTP | Axios |

### Backend (`/backend`)
| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8.3 |
| Admin Panel | Vue 3 + Inertia.js |
| Database | PostgreSQL |
| Auth | Laravel session auth |
| File Storage | Laravel Storage (local, `public` disk) |
| API | Laravel RESTful API + JSON Resources |

---

## Project Structure

```
artist-site/
├── frontend/               # Public-facing Vue 3 SPA
│   └── src/
│       ├── components/     # UI components (Header, Footer, etc.)
│       ├── composables/    # useApi, etc.
│       ├── i18n/           # EN / MS / JA locale files
│       ├── pages/          # Home, Members, Releases, Schedule, etc.
│       ├── types/          # TypeScript interfaces
│       └── utils/          # Helpers, constants
│
└── backend/                # Laravel API + Admin panel
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── Admin/      # Inertia admin controllers (CRUD)
    │   │   │   └── Api/        # Public JSON API controllers
    │   │   ├── Middleware/     # HandleInertiaRequests
    │   │   └── Resources/      # API JSON transformers
    │   └── Models/             # Member, Release, News, Video, Event
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── resources/
    │   ├── css/app.css
    │   ├── js/
    │   │   ├── Pages/
    │   │   │   ├── Auth/Login.vue
    │   │   │   └── Admin/
    │   │   │       ├── Dashboard.vue
    │   │   │       ├── Members/   Index, Create, Edit
    │   │   │       ├── News/      Index, Create, Edit
    │   │   │       ├── Releases/  Index, Create, Edit
    │   │   │       ├── Videos/    Index, Create, Edit
    │   │   │       └── Events/    Index, Create, Edit
    │   │   ├── Layouts/AdminLayout.vue
    │   │   └── Components/Admin/   # Shared form components
    │   └── views/app.blade.php     # Inertia root template
    ├── routes/
    │   ├── api.php         # Public API routes (/api/*)
    │   └── web.php         # Admin routes (/admin/*)
    └── storage/app/public/ # Uploaded & seeded images
        ├── members/
        ├── releases/
        ├── sister-groups/
        └── events/
```

---

## Admin Panel

The admin panel is built with **Vue 3 + Inertia.js** and lives at `/admin`.

**Login:** `/admin/login`
**Default credentials:** `admin@klp48.my` / (set via seeder using `User::factory`)

### Resources managed
- Members (photo upload, hobbies tags, social links, multi-language)
- News (rich content, featured/published toggles, multi-language)
- Releases (cover art, track list repeater, streaming links, multi-language)
- Videos (YouTube ID, thumbnail, multi-language)
- Events (date range, venue, ticket URL, multi-language)

### Translation support
Each resource supports optional Malay (MS) and Japanese (JA) translations via a collapsible Translations section. Translations are stored in a polymorphic `translations` table.

---

## Getting Started

### Backend

```bash
cd backend

# Install PHP dependencies
composer install

# Copy env and generate key
cp .env.example .env
php artisan key:generate

# Configure database in .env (PostgreSQL)
# DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Run migrations and seeders
php artisan migrate --seed

# Create storage symlink
php artisan storage:link

# Install JS dependencies and build admin assets
npm install
npm run build

# Start development server
php artisan serve
```

### Frontend

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

### Backend Dev Mode (all-in-one)

```bash
cd backend
composer run dev
```

This starts `php artisan serve`, queue worker, log watcher, and Vite in parallel.

---

## Public API

The backend exposes a JSON API at `http://localhost:8000/api`:

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/members` | All members |
| GET | `/api/members/{slug}` | Single member |
| GET | `/api/news` | News articles |
| GET | `/api/news/{slug}` | Single article |
| GET | `/api/releases` | Releases |
| GET | `/api/videos` | Videos |
| GET | `/api/events` | Events |

Add `?lang=ms` or `?lang=ja` to any endpoint for translated responses.

---

## Image Storage

All content images (member photos, release covers, event images) are stored in `backend/storage/app/public/` and served via `backend/public/storage/` (symlink).

The seeded images are pre-populated from the original `/images/` folder which has since been removed. New images uploaded through the admin panel follow the same storage pattern automatically.

---

## License

Copyright © 2026 KLP48. All rights reserved.

## Contact

- Email: support_klp48@twopointzero.world
- Twitter: @KLP48official

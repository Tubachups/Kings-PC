# King's PC

Laravel 12 + Inertia Vue 3 e-commerce application for PC components, AI-assisted PC build generation, and social login.

## Tech Stack

- Backend: Laravel 12, PHP 8.3, Fortify, Socialite
- Frontend: Vue 3, Inertia.js v2, Tailwind CSS v4, Vite
- UI: Reka UI + custom components
- Data: MySQL/SQLite (Laravel DB layer), optional Redis
- Testing/Quality: Pest, PHPUnit, Pint, ESLint, Prettier

## Core Features

- Shop pages (`/`, `/components`, `/builds`, category pages)
- Infinite-scrolling builds feed
- AI builder endpoint (`POST /builder/ai`) for budget-aware part suggestions
- Admin product management routes under `/admin/products`
- Cart, checkout, and orders flow
- Google and Facebook OAuth via Socialite

## Project Structure

- `app/Http/Controllers` - application controllers (shop, auth, admin)
- `resources/js/pages` - Inertia page components
- `resources/js/components` - reusable Vue components
- `routes/web` - modular route definitions
- `database/migrations` - schema changes

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+ and npm
- Database server (MySQL recommended in local Laragon setup)

## Installation

1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Install JS dependencies:
   ```bash
   npm install
   ```
3. Configure environment:
   - Ensure `.env` exists and is configured for DB/app URL.
   - Set OAuth and AI keys (see Environment Variables below).
4. Generate app key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. Start development servers:
   ```bash
   composer run dev
   ```

## Useful Commands

- Start backend + queue + Vite:
  ```bash
  composer run dev
  ```
- Frontend dev only:
  ```bash
  npm run dev
  ```
- Production build:
  ```bash
  npm run build
  ```
- Run tests:
  ```bash
  php artisan test --compact
  ```
- Format PHP:
  ```bash
  vendor/bin/pint --dirty --format agent
  ```

## Route Highlights

- Shop: defined in `routes/web/shop.php`
- Social login: defined in `routes/web/socialite.php`
- Admin products: defined in `routes/web/admin/products.php`
- Main route entry: `routes/web.php`

## Notes

- OAuth callbacks are unified in `App\Http\Controllers\Auth\SocialiteController`.
- OAuth IDs persisted on users:
  - Google: `google_id`
  - Facebook: `fb_id`


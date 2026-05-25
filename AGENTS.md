# Invoice Generator — Agent Notes

## Stack
Laravel 12, PHP 8.2+, Vite 7, Tailwind CSS 4, Bootstrap 5, AdminLTE 3, barryvdh/laravel-dompdf.

## Dev Commands
```bash
composer install && npm install
php artisan key:generate
php artisan migrate
composer run dev   # starts: php artisan serve + queue:listen + pail + npm run dev (4 concurrent processes)
composer test      # clears config cache then runs php artisan test
npm run build       # production Vite build
npx pint             # format PHP (laravel preset, StyleCI config)
```

## Testing
- PHPUnit 11.5.3, Feature + Unit suites in `tests/`
- Uses **SQLite in-memory** (DB_CONNECTION=sqlite, DB_DATABASE=:memory:); no external DB needed
- Run a single test: `./vendor/bin/phpunit --filter TestName`

## Code Style
- StyleCI with Laravel preset; `no_unused_imports` is **disabled** — do not add it
- PHP formatting via `npx pint`

## Important Env Notes
- `.env` already exists — do NOT overwrite it with `cp .env.example .env` (no .env.example is present)
- SESSION_DRIVER, CACHE_STORE, and QUEUE_CONNECTION all default to `database`
- MAIL_MAILER is smtp with Gmail; MAIL_PASSWORD is an **app password**, not the account password
- REDIS_CLIENT is `phpredis`; if phpredis extension is missing, agents may need to fall back to `predis`

## Architecture
- Single-package Laravel app (no monorepo)
- Routes: `routes/web.php` (web), `routes/console.php` (scheduled/commands)
- Bootstrap: `bootstrap/app.php` (Laravel 12 style, no Kernel.php)
- Entry points: `artisan` (CLI), `public/index.php` (HTTP)

## Quirks
- Vite config (`vite.config.js`) uses `resources/sass/app.scss` and `resources/js/app.js` as inputs (NOT the default Laravel 11+ `resources/css/` paths)
- AdminLTE 3.15 is published via `jeroennoten/laravel-adminlte`; custom views live in `resources/views/vendor/adminlte/`

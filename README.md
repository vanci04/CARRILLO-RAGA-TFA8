# TFA8 — Ember Café Resource Controllers and Authentication

Laravel 12 implementation of IT0049 TFA8, continued from the TFA7 account-management work.

## Run locally

1. Double-click **START.cmd** in this folder.
2. Open <http://127.0.0.1:8008>.
3. Keep the launcher window open while using the app; press Ctrl+C to stop it.

No assessment selection is needed. Each assessment has a different port, so all three can run together. Restarting preserves existing data.

## Assignment implementation

Customer and user accounts use Laravel resource controllers and `Route::resource()`. Official Laravel Breeze scaffolds registration, login, logout, password reset, password confirmation, and email verification. The `auth` middleware protects every customer and user management route while the overview, about page, and POS remain public. Account CRUD, validation, and prepared avatar uploads from TFA7 are retained.

## Fresh installation

The prepared folder already includes the installed libraries and database. For a fresh source-only copy, install PHP 8.2 or newer and Composer 2, then run the following commands from this directory:

```sh
composer install
```

Copy `.env.example` to `.env`, then run:

```sh
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8008
```

Seeded login: `ella@example.com` / `password`. PHP 8.2 or newer is required. No frontend build is needed because the authentication views use the project’s existing CSS.

## Checks

```sh
php artisan test
php artisan route:list --except-vendor
```

Official Breeze, Intervention Image, and the locked Symfony console source are retained under `packages/` so installation stays reproducible when Packagist is unavailable.

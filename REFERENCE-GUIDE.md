# Ember Café

A café themed Laravel application for IT0049 Technical Formative Assessments 5 and 6. The four required pages share a responsive Blade layout. An additional point of sale supports a café menu, order quantities, cash checkout, saved orders, and printable receipts.

## Open the prepared local project

On the computer where this project was prepared, PHP is available at `C:\xampp\php\php.exe`. Dependencies and a seeded SQLite database are already installed in the project folder.

1. Double-click **Start Cafe.cmd**.
2. Choose **5** for the static array assessment or **6** for the database assessment.
3. Open <http://127.0.0.1:8000>.
4. Keep the launcher window open. Press Ctrl+C to stop it.

If a server is already running on port 8000, stop that server before opening the launcher. The launcher runs migrations and repeatable seeders; it does not erase existing orders.

## Install from the source ZIP or a fresh clone

Requirements: PHP 8.2 or later with PDO SQLite, mbstring, OpenSSL, fileinfo, DOM, and XML; Composer 2. Laravel 12 is used to match the installed XAMPP PHP 8.2. The supplied tasks require Laravel without mandating its major version. Framework support details: <https://laravel.com/docs/12.x/releases>.

Run these commands from the project directory:

```sh
composer install
```

Copy `.env.example` to `.env`, then run:

```sh
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan serve
```

If PHP is not in your Windows PATH, replace `php` with `C:\xampp\php\php.exe`. Use the launcher after installing the dependencies. The CSS and JavaScript are local assets in `public/`; no Node, Vite, external fonts, image services, or frontend build is required.

## Demonstrate the two assessments

The same project preserves both learning stages. Choose a mode in the launcher, or set this value in `.env`:

```dotenv
# TFA5
CAFE_DATA_SOURCE=arrays

# TFA6 (default; use one value at a time)
CAFE_DATA_SOURCE=eloquent
```

After changing `.env`, run `php artisan config:clear` and restart the server. The launcher sets its selected mode for that server process and takes precedence over `.env`.

In **TFA5**, `CustomerController::samples()` and `UserController::samples()` define six static records each. Their `index()` methods pass the arrays to the account view, which renders them with `@foreach`. The four assignment pages do not query the database in this mode. The extra POS checkout still needs the orders table.

In **TFA6**, the account controllers query `Customer::orderBy(...)->get()` and `User::orderBy(...)->get()`. Both results are normalized to arrays for the same Blade view. The migrations create the schemas; `CustomerSeeder` and `UserSeeder` independently populate six records each. The username has a database unique constraint. The additional `role` field retains the TFA5 staff display. The seeder can be run again without duplicating its sample records.

## Assignment objective checklist

| Objective | Implementation |
| --- | --- |
| Install Laravel and run locally | Composer project, `artisan`, `composer.lock`, local launcher |
| Use Artisan to inspect application files | `php artisan about`, `php artisan route:list`, `php artisan migrate:status` |
| Generate controllers and models with Artisan | See equivalent scaffold commands below; completed files are included |
| Landing page `/` | `PageController::home`, `resources/views/home.blade.php` |
| About page `/about` | `PageController::about`, `resources/views/about.blade.php` |
| Customer Accounts `/customers` | `CustomerController::index`, shared `accounts.blade.php` |
| User Accounts `/users` | `UserController::index`, shared `accounts.blade.php` |
| All four routes and navigation | `routes/web.php`, `resources/views/layouts/app.blade.php` |
| At least five static records per account page | Six samples in each account controller in arrays mode |
| Blade iteration | `@foreach($records as $record)` in `accounts.blade.php` |
| Customer migration | `full_name`, `email`, `phone` in the account schema migration |
| User migration | Unique `username`, `full_name`, plus display `role` |
| Seed five or more records per table | Six customers and six staff via `database/seeders` |
| Eloquent models | `app/Models/Customer.php` and `User.php` |
| Database retrieval and display | Account controller Eloquent queries in eloquent mode |
| Raw source and reproducible database | Full source, lockfile, migrations, and seeders included |
| GitHub repository link | Requires publishing to your selected GitHub repository |
| Live hosted application link | Requires deploying to your selected PHP hosting account |

Equivalent scaffold commands for learning the generator workflow (the resulting files are already implemented, so do not overwrite them):

```sh
php artisan make:controller PageController
php artisan make:controller CustomerController
php artisan make:controller UserController
php artisan make:model Customer -m
php artisan make:seeder CustomerSeeder
php artisan make:seeder UserSeeder
```

The account schema is implemented in `database/migrations/0001_01_01_000000_create_users_table.php`, which creates both tables and rolls both back. The rest of the migrations provide framework tables and the additional orders table.

## Test and demonstrate

```sh
php artisan test
php artisan route:list --except-vendor
php artisan view:cache
```

The feature suite covers the required pages, static array mode, database record retrieval, repeatable seeding, server calculated prices, insufficient payment, invalid products and quantities, duplicate checkout retries, and missing receipts. Tests use an isolated in-memory SQLite database.

Suggested classroom walkthrough:

1. Start in TFA5 mode. Visit all four pages and show the sample arrays and Blade loop.
2. Start in TFA6 mode. Show migration status, the two models, seeders, and Eloquent queries.
3. Search for Isabel on Customers and a barista on Staff accounts. Try a search with no match.
4. Open Point of sale. Filter by category, search, and add two Spanish Lattes and one Butter Croissant.
5. The total is ₱425. Enter ₱500 cash. The change is ₱75.
6. Complete the order and inspect the saved receipt. Use Print receipt to open the browser print dialog.

Prices and payment values are integer centavos on the server. The browser sends only product IDs and quantities; submitted prices are ignored. Cash must cover the order, quantities must be 1–99, and repeated requests with the same checkout token return the existing receipt. Orders remain available at `/receipts/{id}` after restarting the server.

## GitHub and hosting handoff

Create an empty GitHub repository, initialize this directory with Git, and publish the source. Include `composer.lock`, migrations, seeders, and this README. Do not commit `.env`, `vendor/`, generated views, logs, or the local SQLite database. The provided `.gitignore` excludes these; database creation is reproducible from migrations and seeders.

For a Laravel-capable host:

1. Configure the web root to the project's **public** folder.
2. Install dependencies with `composer install --no-dev --optimize-autoloader`.
3. Create the host's `.env` with `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` set to the HTTPS site URL, a newly generated `APP_KEY`, and `CAFE_DATA_SOURCE=eloquent`.
4. Configure a persistent SQLite database using an absolute `DB_DATABASE` path, or use the host's MySQL credentials with `DB_CONNECTION=mysql`.
5. Keep `SESSION_DRIVER=file`, `CACHE_STORE=file`, and `QUEUE_CONNECTION=sync`. Allow the PHP process to write to `storage/`, `bootstrap/cache/`, and the SQLite database directory if applicable.
6. Run `php artisan migrate --seed --force` and `php artisan config:cache`.
7. Verify the four required URLs, POS checkout, and receipt before submitting the repository and HTTPS links.

The project has not been uploaded or deployed. No repository URL or hosting URL is fabricated. Fill in your student/group details in the assessment submission separately.

## Scope

This is a classroom demonstration with fictional sample contacts. Staff accounts are directory records rather than login credentials. Authentication, customer editing, stock control, tax invoicing, and payment gateway integration are outside the two supplied assessments. Keep public demonstrations limited to sample data.

## References

- Laravel routing: <https://laravel.com/docs/12.x/routing>
- Blade templates: <https://laravel.com/docs/12.x/blade>
- Migrations: <https://laravel.com/docs/12.x/migrations>
- Eloquent: <https://laravel.com/docs/12.x/eloquent>
- Seeding: <https://laravel.com/docs/12.x/seeding>

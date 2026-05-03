# Invoicely

A self-hosted invoicing and expense-tracking app for freelancers. Built on Laravel 12 + Filament 3, with a public client-facing invoice preview, PDF generation, and a balance dashboard that tracks money in (paid invoices) against money out (paid bills).

---

## Features

### Invoices (incoming)
- Create, edit and manage invoices through the Filament admin at `/admin`.
- Multi-currency support (USD, GBP — extend in `app/Helpers/CurrencyHelper.php`).
- Auto-numbered with a configurable prefix (`Company Settings → Invoice prefix`).
- Line items with drag-to-reorder; quantity × unit rate auto-calculates total.
- Tax amount is editable; subtotal and total recompute reactively.
- **Capped total**: optional override that lets you bill less than the line items add up to (e.g. CEO-imposed cap on a salary invoice). The PDF and public preview show a transparent "Adjustment" line so the client sees the breakdown.
- **Public share link**: every invoice gets a 64-char-hex token URL (`/invoice/preview/{token}`) the client can open without logging in.
- **View tracking**: the invoice flips from `unread` → `viewed` the first time a guest opens the share link. Authenticated admin previews don't trip the flag, so "viewed" actually means the client looked.
- **Email**: one-click "Send Email" action on the invoice list. Uses the customer's address; the button hides itself once `email_sent_at` is set so you don't double-send. Sending also bumps `draft → sent`.
- **PDF download**: rendered with Spatie Browsershot (headless Chromium). Available from both the admin and the public preview link.
- "Mark as Paid" action with confirmation modal.

### Bills (outgoing)
- Track recurring and one-off vendor bills (Hetzner, AWS, GitHub, etc.).
- Fields: vendor, category, description, amount, currency, due/paid dates, recurring flag, notes.
- "Mark as Paid" action.
- Filter by status, category, recurring.

### Dashboard
- **Balance** widget: Income (paid invoices) − Expenses (paid bills) = Net (color-coded green/red).
- **Invoice stats** widget: Total earned, Outstanding, Overdue, Paid this month.
- **Monthly revenue chart**: 12-month line chart of paid invoices.
- **Recent invoices** table.

### Customers
- Standard CRUD (`/admin/customers`).
- Used as the recipient of invoices.

---

## Tech stack

| Layer | Choice |
|---|---|
| Backend | Laravel 12, PHP 8.4 (Docker) / ^8.2 (composer constraint) |
| Runtime | FrankenPHP + Laravel Octane (production), `php artisan serve` (dev) |
| Admin UI | Filament 3 |
| Frontend assets | Tailwind v4, Vite 7 |
| PDF | Spatie Browsershot (local Chromium) **or** Cloudflare Browser Rendering — selectable via `LARAVEL_PDF_DRIVER` |
| DB | SQLite by default; Postgres also wired (Dockerfile installs `pdo_pgsql`) |
| Tests | Pest 3 |
| Style | Laravel Pint, see `pint.json` |

---

## Quick start

### Prerequisites

- PHP 8.2+ with `pdo_sqlite`, `intl`, `gd`, `mbstring` (Herd / Valet on macOS works out of the box).
- Composer 2.
- Node 22 + npm.
- For the **Browsershot** PDF driver: a Chromium binary on the host. On macOS, Herd installs it; on Linux servers, install `chromium-headless-shell` (or `chromium-browser`) and run `npm install` in the project root so `node_modules/puppeteer` exists.
- For the **Cloudflare** PDF driver: a Cloudflare account with Browser Rendering enabled, an API token with the `Browser Rendering: Edit` scope, and the account ID.

### Install

```bash
git clone git@github.com:kevariable/invoicely.git
cd invoicely

composer install
npm install

cp .env.example .env
php artisan key:generate

# Allowlist your email for the admin panel (config/app.php → can_access_panel)
echo 'APP_CAN_ACCESS_PANEL=you@example.com' >> .env

# Migrate + populate with realistic local data
composer fresh
```

That last step gives you:
- Admin user `kevariable@gmail.com` / `password` (change this — see [Production safety](#production-safety))
- ByteHire Limited as the default `CompanySetting` (edit via `/admin/company-settings`)
- 8 customers, ~28 invoices across paid/sent/overdue/draft, 30 sample bills

### Run

```bash
composer dev
```

Runs four processes side by side via `concurrently`: `php artisan serve`, queue listener, `pail` log tail, and `vite`. Visit `http://localhost:8000/admin` (or whichever Herd / Valet domain — e.g. `http://invoicely.test/admin`) and log in.

---

## Common commands

```bash
# Dev
composer dev                       # all-in-one: server + queue + logs + vite
composer fresh                     # migrate:fresh --seed (DB reset + seed)
php artisan migrate                # apply pending migrations only
php artisan tinker                 # REPL

# Tests
composer test                      # config:clear + artisan test
./vendor/bin/pest                  # direct (matches CI)
./vendor/bin/pest --filter=name    # by test name
./vendor/bin/pest tests/Feature    # by directory

# Code style
vendor/bin/pint                    # auto-fix
vendor/bin/pint --test             # check only (CI mode)

# Assets
npm run dev                        # vite dev server
npm run build                      # production build

# Docker
docker compose up --build          # FrankenPHP + Octane on host port 8989
```

`phpunit.xml` forces `DB_CONNECTION=sqlite` with `:memory:` for tests, plus `array` cache/mail and `sync` queue — no real DB or external services needed to run the suite. The CI workflow at `.github/workflows/tests.yml` runs the same `pest` command on push/PR.

---

## Project structure

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── BillResource.php          # Vendor bills (expenses)
│   │   ├── CustomerResource.php
│   │   ├── CompanySettingResource.php
│   │   └── InvoiceResource.php       # Main invoice CRUD + actions
│   └── Widgets/
│       ├── BalanceOverview.php       # Income / Expenses / Net
│       ├── InvoiceStatsOverview.php  # Total / Outstanding / Overdue / This month
│       ├── MonthlyRevenueChart.php
│       └── RecentInvoicesTable.php
├── Helpers/CurrencyHelper.php        # Single source of truth for currency list
├── Http/Controllers/
│   └── InvoicePreviewController.php  # Public /invoice/preview/{token} + PDF
├── Mail/InvoiceNotification.php
├── Models/                           # Eloquent — where state lives
│   ├── Bill.php
│   ├── CompanySetting.php            # Singleton via firstOrCreate
│   ├── Customer.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   └── User.php
└── Providers/Filament/AdminPanelProvider.php

src/                                  # Domain layer (PSR-4: Invoice\)
├── Base/                             # ValueObject, DataReadonly, DataHydration
├── Customer/{Application,Domain}/
└── Invoice/
    ├── Application/Data/             # DTOs (Valinor-hydrated)
    └── Domain/
        ├── Actions/GenerateInvoiceAction.php   # Browsershot PDF
        ├── Contracts/                # Currently declared but unbound
        └── Primitives/               # Value objects (InvoiceId, Amount, ...)

resources/views/
├── filament/brand-logo.blade.php     # Inline SVG wordmark
├── invoice-pdf-browsershot.blade.php # Active PDF template
├── invoice-pdf-dompdf.blade.php      # Legacy DomPDF template
└── invoice-public-preview.blade.php  # Client-facing share link

database/
├── factories/                        # User, Customer, Invoice, InvoiceItem, Bill
├── migrations/
└── seeders/DatabaseSeeder.php        # Local-only data dump
```

### Architectural notes

- `App\` (state, controllers, Filament) and `Invoice\` (`src/`, DDD-flavoured DTOs + value objects) are two parallel namespaces. Behaviour and persistence live on the Eloquent models in `app/Models/`. The domain layer mostly provides DTOs and `GenerateInvoiceAction` (PDF). Don't introduce a service-binding layer for `Domain/Contracts/*` interfaces unless you're also wiring real implementations.
- `CompanySetting::getSettings()` is `firstOrCreate([])` — a true single-row singleton. Use it everywhere instead of querying `CompanySetting` directly.
- Currency: extend `App\Helpers\CurrencyHelper::CURRENCIES`, not migrations.
- Filament panel: single panel `admin`, top navigation, Neutral palette, brand logo from `resources/views/filament/brand-logo.blade.php` (currentColor = light/dark in one asset). Resources/Pages/Widgets are auto-discovered.
- Authentication is gated through Filament's `->login()`. The User model implements `canAccessPanel()` against `config('app.can_access_panel')`, which reads `APP_CAN_ACCESS_PANEL` from env (comma-separated allowlist of emails).

### PDF rendering

`Invoice\Invoice\Domain\Actions\GenerateInvoiceAction` dispatches on `config('pdf.driver')` (env: `LARAVEL_PDF_DRIVER`):

- **`browsershot`** (default) — renders locally via Spatie Browsershot. Needs a Chromium binary and `node_modules/puppeteer`. Best for dev and self-hosted servers where you control the runtime.
- **`cloudflare`** — POSTs the rendered HTML to Cloudflare's Browser Rendering REST API and returns the resulting PDF. No local Chromium required, which makes it the right choice on serverless hosts. Set `CLOUDFLARE_ACCOUNT_ID` and `CLOUDFLARE_API_TOKEN` in env.

---

## Production safety

A few things to be aware of when deploying:

- **Migrations are additive only**. The most recent set adds nullable columns and a new table — no column drops or data backfills that could lose data.
- **`DatabaseSeeder` is gated for local/testing**. It early-returns unless `app()->environment(['local','testing'])`. Even if someone accidentally runs `php artisan db:seed` on prod, it no-ops with a warning. The seeded admin user (`kevariable@gmail.com / password`) and the 30+ fake customers/invoices/bills cannot leak into production.
- **`composer fresh` on production would drop all tables**. Laravel's `migrate:fresh` already prompts unless `--force` is passed, and the composer script does not pass `--force`. Don't add it.
- **Override the seeded admin** the first time you boot a fresh prod DB: create a real user via `php artisan tinker` and add their email to `APP_CAN_ACCESS_PANEL`. Then change the seeder default if you keep using it locally.
- **Browsershot needs Chromium at runtime**. The bundled `docker/Dockerfile` installs `chromium-headless-shell`; if you deploy outside Docker, install Chromium yourself.

---

## Deployment (Docker)

```bash
docker compose up --build
```

This builds an image based on `dunglas/frankenphp:php8.4`, installs Composer + Node + Chromium, and starts the app on host port 8989 (forwarded from container 8000). The entrypoint runs `php artisan octane:start --server=frankenphp`. Cron is set up via `docker/crontab`, supervisord via `docker/supervisord.conf`. Mounts the working directory into the container with `delegated` for fast local dev.

For production-like deploys, change the entrypoint volume mount and provide a real `.env` with:
- `APP_ENV=production`
- `APP_DEBUG=false`
- A strong `APP_KEY`
- `DB_CONNECTION=pgsql` (or your choice) with credentials
- `APP_CAN_ACCESS_PANEL=you@example.com,...`
- Mail credentials

Run migrations on first boot: `php artisan migrate --force`.

---

## CI

Two GitHub Actions workflows in `.github/workflows/`:

- **`linter`** — runs `vendor/bin/pint --test` on PRs to `main`/`develop`. Style violations fail the job.
- **`tests`** — installs PHP 8.4 + Node 22, builds assets, runs `./vendor/bin/pest`.

Both require the `FLUX_USERNAME` / `FLUX_LICENSE_KEY` repository secrets so `composer install` can fetch `livewire/flux` from `composer.fluxui.dev`. PRs from forks won't have access to those secrets and will fail at `composer install` — open issues from the main repo, not forks.

---

## Contributing

- Branch from `main` with a `feat/`, `fix/` or `docs/` prefix.
- Keep behaviour on Eloquent models in `app/Models/` unless reused outside Filament.
- Run `vendor/bin/pint && ./vendor/bin/pest` before pushing.
- Use the existing PR template style (Summary + Test plan).

---

## License

MIT.

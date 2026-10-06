# SiMonika

SiMonika is an application inventory and project monitoring system for an operations team. It records deployed applications, flexible application metadata, employees, projects, timelines, internship participants, and administrative activity.

This repository is a sanitized portfolio edition of an internship project that originally stopped before it was ready for public evaluation. It contains no government database, production credentials, internal hostnames, or employee records.

## Problem and users

Operational application data often ends up spread across spreadsheets and disconnected notes. That makes it difficult to answer basic questions: which applications are active, who maintains a project, what technology is in use, and when a milestone is due.

SiMonika gives two user roles a single server rendered workspace:

- **Admin** maintains applications, attributes, employees, projects, timelines, and internship records.
- **Super Admin** has the same operational access plus administrator and activity log management.

## Implemented workflows

- Secure login, logout, remember me, and password reset
- Role protected administrator management
- Application inventory with typed, reusable custom attributes
- Formula safe streaming CSV export
- Employee, category, project, and project timeline management linked to the application inventory
- Internship participant records with date and headcount validation
- Activity logs for authentication and critical mutations
- Dashboard aggregation for application status, type, platform, and developer

## Engineering highlights

- Laravel Form Requests validate application and timeline commands.
- Focused services keep application, attribute, account, employee, project, timeline, and internship transactions outside HTTP controllers.
- Typed attribute values are checked before persistence; incompatible definition changes are rejected.
- Password reset tokens are hashed, expire after 60 minutes, and are single use.
- CSV cells that could become spreadsheet formulas are neutralized before streaming.
- Database migrations support both a clean install and a complete rollback.
- An end-to-end feature test follows one record from typed inventory metadata through a linked project, owner, completed timeline, dashboard, detail view, and CSV export.
- CI enforces tests, formatting, and source file length limits.

## Stack

| Area | Technology | Reason |
| --- | --- | --- |
| Backend | PHP 8.2+, Laravel 12 | Mature validation, authentication, ORM, and server rendered workflows |
| UI | Blade, Bootstrap 5, JavaScript modules | Small deployment surface without a frontend build step |
| Data | SQLite locally; MySQL configuration available | Fast local reproduction with a path to managed relational storage |
| Export | Native streamed CSV | Constant memory iteration and no spreadsheet parser dependency |
| Quality | PHPUnit, Laravel Pint, GitHub Actions | Repeatable behavioral and style gates |
| Runtime | Apache container or local PHP server | Reproducible evaluation and a simple local workflow |

## Quick start

Requirements: PHP 8.2 or newer with SQLite, plus Composer.

```bash
git clone https://github.com/abiekaputra/simonika.git
cd simonika
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate
php artisan serve
```

Open `http://127.0.0.1:8000`.

### Docker

```bash
docker compose up --build
```

Open `http://127.0.0.1:8080`. The entrypoint creates an application key, initializes a persistent SQLite file, and runs outstanding migrations.

## Optional local account

Demo seeding is disabled by default. Set local credentials in `.env`:

```dotenv
SIMONIKA_DEMO_SEED=true
SIMONIKA_DEMO_ADMIN_EMAIL=admin@example.test
SIMONIKA_DEMO_ADMIN_PASSWORD=choose-a-local-password
```

Then run `php artisan db:seed`. This creates a synthetic catalogue plus two local roles:

- the configured super-admin email;
- `operator@example.test`, using the same configured password.

Never enable the demo seeder in a public deployment.

## Verification

```bash
composer quality
composer audit --locked
```

`composer quality` checks the source file limits, executes the behavioral test suite against an in memory SQLite database, and verifies formatting. The suite covers authentication, authorization, password reset, transactional mutations, typed values, CSV safety, linked project workflows, timeline consistency, audit retention, employee uniqueness, operational dates, screen rendering, and guest access.

## Design documentation

- [Product scope](docs/product-scope.md)
- [Acceptance criteria](docs/acceptance-criteria.md)
- [Architecture](docs/architecture.md)
- [Data model](docs/data-model.md)
- [HTTP interface](docs/http-interface.md)
- [Testing and failure handling](docs/testing-and-failures.md)
- [End-to-end product flow](docs/end-to-end-flow.md)
- [Browser validation](docs/browser-validation.md)
- [Engineering decisions and tradeoffs](docs/engineering-decisions.md)
- [Security policy](SECURITY.md)

## Project status

Product definition, backend/domain hardening, responsive UI, and end-to-end local integration are complete for portfolio evaluation. Both roles, every primary screen, narrow-screen behavior, domain invariants, linked inventory and project workflows, and synthetic data setup have been validated. This repository makes no deployment or production-readiness claim. Known scale boundaries remain client-side inventory filtering and synchronous email delivery.

## License

[MIT](LICENSE)

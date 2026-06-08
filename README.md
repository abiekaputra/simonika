# SiMonika — Application Monitoring System

A Laravel-based web application for monitoring and tracking government agency (OPD) applications, covering employee management, project tracking, activity timelines, and intern data collection.

## Features

- **Dashboard** — overview of active/inactive application counts and last update log
- **Application Management** — full CRUD for government applications with dynamic custom attributes and Excel export
- **Employee Management** — data of employees involved in application development and management
- **Project Management** — track development projects by category and status
- **Timeline** — activity history log per application
- **Intern Data Collection** — manage intern participant records
- **Activity Log** — audit trail of user actions (Super Admin only)
- **Authentication** — login, registration, forgot password, and profile management
- **Data Export** — download application data as Excel

## User Roles

| Role | Access |
|------|--------|
| **Admin** | Dashboard, applications, employees, projects, timelines, intern data, profile |
| **Super Admin** | All Admin features + user management, activity log, log export |

## Tech Stack

- **Framework** — Laravel 11
- **Frontend** — Blade, Tailwind CSS, Vite
- **Database** — MySQL
- **Language** — PHP 8.x

## Installation

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/abiekaputra/simonika.git
cd simonika

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install && npm run build

# 4. Copy environment configuration
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env
#    Set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 7. Run migrations
php artisan migrate

# 8. (Optional) Run seeders
php artisan db:seed

# 9. Start development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Project Structure

```
app/
├── Http/Controllers/      # Module controllers
├── Models/                # Eloquent models
├── Exports/               # Excel export classes
├── Imports/               # Data import classes
├── Mail/                  # Mailable classes
└── Traits/                # Reusable traits
database/
├── migrations/            # Database schema
└── seeders/               # Seed data
resources/views/           # Blade templates
routes/web.php             # Route definitions
```

## License

Built for academic purposes and portfolio development.

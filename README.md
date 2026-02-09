# Able Pro - CRM Module

Laravel 11+ | Vue 3 | Inertia.js | Vuetify 3 | Able Pro Admin Template

## About

Able Pro is a comprehensive CRM (Customer Relationship Management) system built with Laravel and Vue 3, featuring the Able Pro admin template implemented with Vuetify 3.

## Features

- **Contact Management** - Manage customer contacts and relationships
- **Client/Account Management** - Track companies and organizations
- **Lead Management** - Capture and convert leads to customers
- **Deal Pipeline** - Visual Kanban board for sales opportunities
- **Activity Tracking** - Log all customer interactions
- **Dashboard & Reports** - Analytics and insights

## Tech Stack

- **Backend:** Laravel 11+, PostgreSQL/SQLite/MySQL
- **Frontend:** Vue 3 (Composition API), Inertia.js, Vuetify 3
- **UI:** Able Pro Admin Template v9.6.2 (Vuetify implementation)
- **Charts:** ApexCharts
- **Icons:** vue-tabler-icons

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- PostgreSQL/SQLite/MySQL

### Installation

1. Clone the repository:
```bash
git clone https://gitlab.com/Kofitelli/able-pro.git
cd able-pro
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Copy environment file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your `.env` file with database credentials

6. Run migrations:
```bash
php artisan migrate
```

7. Build frontend assets:
```bash
npm run dev
# or for production:
npm run build
```

8. Start the development server:
```bash
php artisan serve
```

## Documentation

- **CRM-QUICK-START.md** - Quick reference guide
- **CRM-IMPLEMENTATION-PROMPTS.md** - Step-by-step implementation prompts
- **CRM-MODULE-ROADMAP.md** - Complete technical roadmap
- **able-pro-analysis.md** - Project structure analysis

## Project Structure

```
app/
├── Http/
│   ├── Controllers/CRM/     # CRM controllers
│   ├── Requests/CRM/        # Form requests
│   └── Resources/CRM/      # API resources
├── Models/                   # Eloquent models
└── Policies/CRM/            # Authorization policies

resources/js/
├── layouts/dashboard/        # Dashboard layout
├── Pages/CRM/                # CRM pages
└── components/CRM/          # Reusable CRM components
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

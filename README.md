# Viviz Finance - Microfinance Management System

A modern microfinance management system built with Laravel 12, Livewire 3, and Tailwind CSS.

## Features

- **Multi-Branch Support** - Manage multiple branches with data isolation
- **Role-Based Access** - Super Admin, Branch Manager, Collection Agent roles
- **Loan Management** - Create, track, and manage loans with automated schedules
- **Payment Collection** - Daily collection tracking with receipt generation
- **Penalty Management** - Automatic penalty calculation for overdue payments
- **Reports** - Branch and loan reports with date filtering
- **Modern UI** - Clean, responsive design with light theme

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire 3, Tailwind CSS, Alpine.js
- **Database**: MySQL 8.0+
- **Auth**: Laravel Breeze + Spatie Permission

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd finance

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=finance
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

## Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@finance.local | password |
| Branch Manager | manager@finance.local | password |
| Collection Agent | agent@finance.local | password |

## Console Commands

```bash
# Mark overdue schedules (run daily)
php artisan schedules:update-overdue

# Apply penalties to overdue payments (run daily)
php artisan penalties:apply

# Generate demo data
php artisan db:seed --class=DemoDataSeeder
```

## Project Structure

```
app/
├── Enums/          # LoanStatus, PaymentMethod, etc.
├── Livewire/       # Livewire components
├── Models/         # Eloquent models
├── Policies/       # Authorization policies
├── Services/       # Business logic services
└── Console/        # Artisan commands

resources/views/
├── layouts/        # App and guest layouts
└── livewire/       # Component views
```

## Running Tests

```bash
php artisan test
```

## License

MIT License

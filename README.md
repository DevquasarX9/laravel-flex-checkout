# Laravel Flex Checkout

A modern, full-stack Laravel + React application featuring comprehensive authentication, user management, and a flexible checkout system with promotional pricing.

## Overview

Laravel Flex Checkout is a production-ready starter kit that combines Laravel's robust backend with React's powerful frontend ecosystem. It provides a complete authentication system with 2FA, user settings management, and a supermarket-style checkout system with product and promotion management.

## Key Features

- **Complete Authentication System**
    - User registration with email verification
    - Login/logout with session management
    - Password reset functionality
    - Two-factor authentication (TOTP)
    - Recovery codes

- **User Settings Management**
    - Profile information updates
    - Password management
    - 2FA configuration
    - Appearance settings

- **Flexible Checkout System**
    - Product management (CRUD)
    - Promotion management with special pricing (e.g., "3 for $1.30")
    - SKU-based checkout
    - Automatic promotion application
    - Sales tracking with detailed receipts
    - Discount and savings breakdown

- **Modern Development Experience**
    - Docker-based development environment
    - Hot module reload (Vite)
    - TypeScript type safety
    - Comprehensive testing with Pest
    - Git pre-commit hooks
    - Code formatting and linting

## Tech Stack

### Backend

- **Laravel 12** - PHP framework
- **PHP 8.4+** - Programming language
- **PostgreSQL 16** - Production database
- **Laravel Fortify** - Authentication
- **Laravel Wayfinder** - Type-safe routing
- **Pest** - Testing framework

### Frontend

- **React 19** - UI framework
- **TypeScript 5.9+** - Type safety
- **Inertia.js 2.2+** - SPA integration
- **Tailwind CSS 4** - Styling
- **Vite 7** - Build tool
- **Radix UI** - Component primitives

### Infrastructure

- **Docker & Docker Compose** - Containerization
- **Nginx** - Web server
- **PHP-FPM** - Application server

## Prerequisites

- Docker & Docker Compose
- Git
- (Optional) Node.js 18+ and PHP 8.4+ for local development without Docker

## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-flex-checkout.git
cd laravel-flex-checkout
```

### 2. Environment Setup

```bash
# Copy the environment file
cp .env.example .env

# Set Docker user permissions (Linux only)
# Add these lines to your .env file:
UID=1000
GID=1000
```

### 3. Start Docker Services

```bash
# Start all services
docker compose up -d

# Install PHP dependencies
docker compose exec php-fpm composer install

# Generate application key
docker compose exec php-fpm php artisan key:generate

# Install JavaScript dependencies
docker compose exec php-fpm npm install

```

### 4. Database Setup

```bash
# Run migrations
docker compose exec php-fpm php artisan migrate

# Seed the database with sample data
docker compose exec php-fpm php artisan db:seed
```

### 5. Build Frontend Assets

```bash
# Development build with hot reload
docker compose exec php-fpm npm run dev

# Or production build
docker compose exec php-fpm npm run build
```

### 6. Access the Application

Open your browser and navigate to:

- **HTTPS**: [https://flexchekout.test](https://flexchekout.test) (self-signed certificate)

Default seeded users will be available for testing.

## Development Commands

### Starting Development Servers

```bash
# Start all services (Laravel + Vite)
composer dev

# Start with SSR support
composer dev:ssr

# Start only Vite dev server
npm run dev

# Start only Laravel server
php artisan serve
```

### Running Tests

```bash
# Run all tests
composer test

# Or using artisan
php artisan test

# Run specific test
php artisan test --filter=CheckoutTest
```

### Code Quality

```bash
# Format PHP code (Laravel Pint)
composer pint

# Lint and fix JavaScript/TypeScript
npm run lint

# Format JavaScript/TypeScript
npm run format

# TypeScript type checking
npm run types
```

### Database Operations

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database with seed data
php artisan migrate:fresh --seed
```

### Docker Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f

# Execute commands in container
docker compose exec php-fpm [command]
```

## Project Structure

```
├── app/
│   ├── Actions/          # Single-purpose action classes
│   ├── Http/
│   │   ├── Controllers/  # Request handlers
│   │   └── Requests/     # Form validation
│   ├── Models/           # Eloquent models
│   └── Services/         # Business logic services
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── css/              # Stylesheets
│   └── js/
│       ├── components/   # React components
│       ├── layouts/      # Page layouts
│       ├── pages/        # Inertia pages
│       └── types/        # TypeScript definitions
├── routes/
│   ├── web.php           # Main routes
│   └── settings.php      # Settings routes
├── tests/
│   ├── Feature/          # Feature tests
│   └── Unit/             # Unit tests
└── docker/               # Docker configuration
```

## Testing

The project uses Pest PHP for testing with comprehensive coverage:

```bash
# Run all tests (32 tests)
composer test

# Run with coverage
php artisan test --coverage
```

**Current Test Coverage:**

- ✅ Authentication flows
- ✅ User settings management
- ✅ Checkout system (pricing, promotions, sales)
- ✅ Product management
- ✅ Form validation
- ⏳ Frontend tests (planned)

## Development Features

- **Hot Module Reload** - Instant feedback with Vite HMR
- **Xdebug Support** - PHP debugging on port 9003
- **Git Hooks** - Pre-commit checks for code quality
- **Type Safety** - Full TypeScript coverage
- **Code Formatting** - Automatic with Pint and Prettier
- **Database Seeding** - Realistic test data included

## Architecture Highlights

- **Tell-Don't-Ask Pattern** - Models expose behavior, not just data
- **Service Layer** - Business logic separated from controllers
- **Type-Safe Routing** - Laravel Wayfinder integration
- **Clean Code** - Functions kept under 20 lines
- **SOLID Principles** - Single responsibility throughout
- **Strict Test Isolation** - Unit tests don't touch database

## Contributing

This project follows PSR-12 coding standards for PHP and uses Prettier for JavaScript/TypeScript. All code must pass linting and tests before committing (enforced by git hooks).

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues, questions, or contributions, please open an issue or pull request on GitHub.

---

**Built with ❤️ using Laravel, React, and modern web technologies.**

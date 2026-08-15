# Transport Management System

A full-featured Transport Management System (TMS) built with **Laravel 12**, **Tailwind CSS v4**, and **Laravel Reverb** for real-time updates. Supports multiple user roles — Admin, Manager, Driver, and Client — with modules for trips, trucks, invoices, payments, expenses, cashbook, and more.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Quick Start with Docker](#quick-start-with-docker)
- [Services & Ports](#services--ports)
- [Default Credentials](#default-credentials)
- [Useful Commands](#useful-commands)
- [Environment Variables](#environment-variables)
- [Running Tests](#running-tests)
- [Troubleshooting](#troubleshooting)

---

## Features

### Authentication & Access Control
- **Multi-role authentication** — login, registration, password reset, and email verification with session-based access
- **Role-based access control (RBAC)** — four distinct roles (Admin, Manager, Driver, Client) gated by role middleware and Laravel policies
- **User administration & approval** — admin user lifecycle management, bulk approval, status toggles, and self-deactivation safeguards
- **Profile, company & security preferences** — per-role profile editing, password updates (with current-password verification), session/device force-termination, and company identity settings
- **Audit & login monitoring** — full activity trail across all modules plus recorded login successes/failures for traceability

### Trip Operations
- **End-to-end trip lifecycle** — create, assign, and manage trips through a controlled status flow (`created → in_progress → completed / cancelled`)
- **Driver field operations** — drivers execute assigned trips: log expenses, record reloads, and submit GPS location from the field
- **Live vehicle tracking** — real-time GPS location submission via driver API with current location and historical position tracking
- **Trip expenses** — driver-entered expenses with manager/admin approval and rejection workflow

### Fleet & Client Management
- **Truck fleet management** — truck registry, status, and availability controls with ownership-checked updates
- **Driver management** — create, approve, activate, and maintain drivers used in trip assignment
- **Client management** — manage transport clients and operational profiles used in trip creation and billing

### Client Portal
- **Client dashboard** — role-specific snapshot of outstanding balances, recent payments, and linked trips
- **Online self-service payments** — clients can view pending trip dues and pay directly from the portal

### Financial Modules
- **Invoicing** — generate trip invoices (blocked for already-invoiced trips) with synchronized due records
- **Payments** — record and reconcile payments; due amounts are recalculated automatically as payments arrive
- **Due management** — dedicated due-record tracking with remaining-due recalculation and settled status
- **Expenses** — trip expense approval/rejection with profit and due calculations
- **Cashbook ledger** — credit/debit ledger entries generated automatically from trip payments, expenses, and spare-part sales
- **Spare parts inventory & sales** — stock management, part sales with atomic stock decrement, low-stock threshold alerts, and automatic cashbook credit entries

### Payment Gateway
- **Pluggable payment gateway factory** — unified `PaymentGatewayFactory` supporting multiple providers
- **bKash integration** — client-facing online bKash checkout with initiate/callback flow, transaction verification, and provider references
- **SSLCommerz integration** — additional online payment gateway provider
- **Offline/manual collection** — fallback offline gateway for manual payment recording (disabled in the client self-service checkout for traceability)
- **Secure, traceable transactions** — payment attempts, webhook records, and provider references stored against each payment

### Real-Time & Notifications
- **Real-time broadcasting** — powered by Laravel Reverb (WebSockets) for live updates
- **Notification system** — in-app notifications triggered by trip status changes and low-stock transitions
- **Multi-channel communication** — queued Email, In-App, Push, SMS, and WhatsApp messaging via a dedicated Communication module

### Analytics & Reporting
- **Role-based dashboards** — operational KPI snapshots tailored to Admin, Manager, Driver, and Client views
- **Reporting** — generate and download operational and financial reports (admin-controlled scope)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3, Laravel 12 |
| Frontend | Tailwind CSS v4, Vite 7 |
| Database | MySQL 8.4 |
| Cache / Queue | Redis 7 |
| WebSockets | Laravel Reverb |
| Web Server | Nginx 1.27 |
| Mail (dev) | Mailpit |
| Containerisation | Docker & Docker Compose |

---

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/install/) ≥ 2.20
- Git

No local PHP, Node, or Composer installation is required.

---

## Quick Start with Docker

### 1. Clone the repository

```bash
git clone https://github.com/funkyd3v/smart-transport-management-system.git
cd transport-management-system
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

The default Docker values in `.env.example` are pre-configured and work out of the box. You only need to change them if you want to customise ports or credentials.

### 3. Build and start all containers

```bash
docker-compose up -d --build
```

This starts eight services: `app`, `web`, `mysql`, `redis`, `queue`, `reverb`, `vite`, and `mailpit`.  
The first build takes a few minutes while Docker downloads images and installs PHP/Node dependencies.

### 4. Run database migrations and seed the admin account

```bash
docker-compose exec app php artisan migrate --seed
```

### 5. Generate the application key (first time only)

```bash
docker-compose exec app php artisan key:generate
```

### 6. Open the application

| Service | URL |
|---|---|
| Application | http://localhost:8000 |
| Vite dev server | http://localhost:5173 |
| Mailpit (email) | http://localhost:8025 |
| Reverb (WebSockets) | ws://localhost:8080 |

---

## Services & Ports

| Container | Image | Host Port | Purpose |
|---|---|---|---|
| `tms-app` | Custom PHP 8.3-FPM | — | Laravel application (PHP-FPM) |
| `tms-web` | nginx:1.27-alpine | **8000** | HTTP entry point |
| `tms-mysql` | mysql:8.4 | **3307** | Database |
| `tms-redis` | redis:7-alpine | **6379** | Cache & sessions |
| `tms-queue` | Custom PHP 8.3-FPM | — | Queue worker |
| `tms-reverb` | Custom PHP 8.3-FPM | **8080** | WebSocket server |
| `tms-vite` | node:20-alpine | **5173** | Vite HMR dev server |
| `tms-mailpit` | axllent/mailpit | **8025** / 1025 | Local mail catcher |

> MySQL is exposed on port **3307** (not 3306) to avoid conflicts with a local MySQL installation.

---

## Default Credentials

After running `php artisan migrate --seed`, log in at **http://localhost:8000/login** with any of the seeded accounts:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@demo.com` | `admin@26` |
| Manager | `manager@demo.com` | `manager@26` |
| Driver | `driver@demo.com` | `driver@26` |

> You can override any of these before seeding by setting the corresponding environment variables in `.env`:
>
> | Variable | Default |
> |---|---|
> | `ADMIN_EMAIL` / `ADMIN_PASSWORD` / `ADMIN_NAME` | `admin@demo.com` / `admin@26` / `System Admin` |
> | `MANAGER_EMAIL` / `MANAGER_PASSWORD` / `MANAGER_NAME` | `manager@demo.com` / `manager@26` / `Manager` |
> | `DRIVER_EMAIL` / `DRIVER_PASSWORD` / `DRIVER_NAME` | `driver@demo.com` / `driver@26` / `Driver` |

---

## Useful Commands

All commands are run inside the `app` container via `docker-compose exec app`.

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed the database
docker-compose exec app php artisan db:seed

# Clear all caches
docker-compose exec app php artisan optimize:clear

# List all routes
docker-compose exec app php artisan route:list --except-vendor

# Open a Tinker REPL
docker-compose exec app php artisan tinker

# Tail application logs
docker-compose logs -f app

# Rebuild containers after Dockerfile changes
docker-compose up -d --build

# Stop all containers
docker-compose down

# Stop and remove volumes (resets the database)
docker-compose down -v
```

---

## Environment Variables

Key variables you may want to change in `.env`:

| Variable | Default | Description |
|---|---|---|
| `APP_KEY` | *(empty)* | Generate with `php artisan key:generate` |
| `APP_URL` | `http://localhost:8000` | Public URL of the app |
| `DB_DATABASE` | `tms` | MySQL database name |
| `DB_USERNAME` | `tms` | MySQL username |
| `DB_PASSWORD` | `tms` | MySQL password |
| `ADMIN_EMAIL` | `admin@demo.com` | Seeded admin email |
| `ADMIN_PASSWORD` | `admin@26` | Seeded admin password |
| `REVERB_APP_KEY` | — | Reverb app key (set in `.env`) |
| `REVERB_APP_SECRET` | — | Reverb app secret (set in `.env`) |

---

## Running Tests

```bash
# Run the full test suite
docker-compose exec app php artisan test --compact

# Run a specific test file
docker-compose exec app php artisan test --compact tests/Feature/ExampleTest.php

# Filter by test name
docker-compose exec app php artisan test --compact --filter=testName
```

---

## Troubleshooting

### Containers exit immediately after starting

Check the logs for the failing service:

```bash
docker-compose logs app
docker-compose logs mysql
```

### 502 Bad Gateway

The `app` container (PHP-FPM) may still be starting. Wait a few seconds and refresh. Check with:

```bash
docker-compose ps
docker-compose logs app
```

### CSS / JavaScript not loading on the dashboard

The Vite dev server must be running. Check its status:

```bash
docker-compose logs vite
```

If the `tms-vite` container is not running, start it:

```bash
docker-compose up -d vite
```

### Database tables missing / migrations not run

```bash
docker-compose exec app php artisan migrate
```

### Resetting the database from scratch

```bash
docker-compose down -v          # removes volumes including mysql-data
docker-compose up -d --build
docker-compose exec app php artisan migrate --seed
```

### Port conflicts

If ports 8000, 5173, 3307, 6379, or 8080 are already in use on your machine, edit the host-side port mappings in `docker-compose.yml` (the left-hand number in `"HOST:CONTAINER"` pairs).

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

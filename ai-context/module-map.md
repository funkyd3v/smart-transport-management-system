# Module Map

## Core Shell
- App Providers
	- AppServiceProvider: menu provider binding, rate limiter trip-location, helper bootstrap
	- AuthServiceProvider: policy bindings for Trip/Client/Driver/Truck
	- ModuleServiceProvider: generic module DI + fallback route auto-load
- Menu System
	- Role menu providers: AdminMenuProvider, ManagerMenuProvider, DriverMenuProvider, ClientMenuProvider
	- MenuResolver picks provider from first URL segment

## Role Experience Modules
- Admin module
	- Responsibilities: platform-level dashboard, users, trips oversight, finance overview/dues/cashbook, drivers, trucks, clients, reports, audit logs, settings
	- Route hub: /admin/* (admin.*)
	- Provider: loads routes/api + views namespace admin::
- Manager module
	- Responsibilities: manager-owned clients/drivers/trucks/trips, profile, trip operations (expense approval, payments, invoice view)
	- Route hub: /manager/* (manager.*)
	- Provider: loads routes/api + views namespace manager::
- Driver module
	- Responsibilities: driver dashboard/profile, assigned trip execution (status, expense, reload, location updates)
	- Route hub: /driver/* (driver.*)
	- Provider: loads routes/api + views namespace driver::
- Client module
	- Responsibilities: client dashboard shell
	- Route hub: /client/* (client.*)

## Domain Operation Modules
- Trip module
	- Responsibilities: trip lifecycle, goods, expenses, payments, invoice generation, due synchronization, trip notifications, vehicle location tracking
	- Route hubs: /admin/trips/*, /admin/invoices/*, /driver/trips/* plus generic REST routes
	- Notable components: actions + DTOs + repository contracts + event/listener workflows
- Spare module
	- Responsibilities: spare inventory CRUD, sales recording, stock tracking, low-stock alerting
	- Route hub: /admin/spare/* (admin.spare.*)
	- Integrations: cashbook credit entries on sales, notifications on low stock via observer
- Cashbook module
	- Responsibilities: cashbook ledger entries and views
	- Route hubs: /cashbook/* and /api/cashbook/*
	- Integrations: listens to trip payments and expense creation

## Supporting CRUD Modules (Generic Resource Pattern)
- AuditLog, Auth, Dashboard, Due, Expense, Invoice, Notification, Payment, Report, Truck
- Common shape: Models + Controllers + Requests + Repositories + Resources + Routes/web.php + Routes/api.php
- Most expose apiResource routes under both / and /api (legacy/generic scaffolding style)

## Policy/Ownership Domains
- Trip permissions: App\Modules\Driver\Policies\TripPolicy (effective)
- Manager ownership policies:
	- ClientPolicy, DriverPolicy, TruckPolicy in app/Modules/Manager/Policies
	- Manager actions often restricted to resources they created/approved

## Critical Route Prefixes By Role
- admin: admin.*
- manager: manager.*
- driver: driver.*
- client: client.*
- spare admin: admin.spare.*

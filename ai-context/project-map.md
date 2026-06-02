# transport-management-system: AI Project Map

## Scope
- Laravel 12 monolith with role-based web app and mixed web/API routes.
- Primary business domains: trip operations, fleet/driver/client management, finance (invoice/payment/due/cashbook), spare inventory/sales.

## Runtime Entry Points
- HTTP bootstrap: bootstrap/app.php
	- Registers routes files: routes/web.php, routes/api.php, routes/console.php, routes/channels.php
	- Registers middleware aliases: role, abilities, ability
	- RedirectIfAuthenticated sends users to role dashboards: admin, manager, driver, client
- Service providers list: bootstrap/providers.php
	- Core: AppServiceProvider, AuthServiceProvider, ModuleServiceProvider
	- Domain providers: Admin, Manager, Driver, Client, Spare, Trip, Cashbook

## App Layer Layout
- app/Modules/* holds business modules (Admin, Manager, Driver, Client, Trip, Spare, etc.)
- app/Menu/* provides role-specific sidebar/navigation definitions
- app/Policies and module policies enforce authorization
- app/Providers wires policies, DI bindings, and route/view loading

## Module Loading Model
- Explicit module providers in bootstrap/providers.php load most critical module routes/views.
- ModuleServiceProvider also scans app/Modules for generic repository interface bindings and fallback route loading.
- Important nuance: if a module has its own provider file at app/Modules/{Module}/Providers/{Module}ServiceProvider.php, ModuleServiceProvider skips route auto-loading for that module (provider is expected to load routes itself).

## Navigation Path Hubs
- Public/auth root: /
- Admin area: /admin/*, route names admin.*
- Manager area: /manager/*, route names manager.*
- Driver area: /driver/*, route names driver.*
- Client area: /client/*, route names client.*
- Spare subdomain (admin): /admin/spare/*, route names admin.spare.*
- Trip admin workflow: /admin/trips/* + /admin/invoices/*
- Driver trip workflow: /driver/trips/*

## Authorization Wiring (Critical)
- Effective Trip policy binding is App\Modules\Driver\Policies\TripPolicy (AuthServiceProvider), not App\Policies\TripPolicy.
- AppServiceProvider also calls Gate::policy for Trip using App\Policies\TripPolicy, but AuthServiceProvider later overrides effective binding.
- Role middleware checks exact user role strings.

## Shared Cross-Module Flows
- Trip status/invoice/payment emit events handled by listeners:
	- TripStatusChanged -> notifications + stop tracking
	- InvoiceGenerated -> create/update due record
	- PaymentRecorded -> update due record and trip due
- Cashbook integrates via listeners and direct service calls:
	- PaymentRecorded listener in Cashbook module
	- Spare sales directly create cashbook credit entries

## Canonical Data Anchors
- Auth user model in auth provider: App\Models\User
- Module-local user model used in manager/driver creation flows: App\Modules\Auth\Models\User
- Primary route key pattern for domain entities: ULID in many modules (for example Trip model route key is ulid)

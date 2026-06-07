# AI Decision Rules

When implementing a feature:

1. Check whether a similar feature already exists.
2. Copy the existing pattern from the nearest module.
3. Never introduce a new architecture unless explicitly requested.
4. Prefer Service + Repository pattern.
5. Prefer FormRequest validation.
6. Prefer Policy-based authorization.
7. Prefer existing Blade and Alpine.js patterns.
8. Use DB transactions for multi-write operations.
9. Use Events for cross-module communication.
10. Reuse existing components before creating new ones.

Priority Order:

Existing Feature Pattern
    ↓
Module Pattern
    ↓
Project Pattern
    ↓
Laravel Convention
    ↓
New Pattern (Last Resort)


# Pattern Selection Guide

Feature Type                  Pattern
--------------------------------------------------
CRUD                          Service + Repository
Complex Workflow              Action + Service + DTO
Validation                    FormRequest
Authorization                 Policy
AJAX Listing                  Partial Blade + JSON
Notifications                 Event + Listener + Job
Multi-table Write             DB Transaction
UI Components                 x-common Components
Role Protection               Middleware + Policy

# Implementation Patterns

## Module Structure Pattern
- Primary pattern is module-first under app/Modules/{Module}.
- Common folders used across many modules:
  - Http/Controllers
  - Http/Requests (plus some legacy Requests folder in older modules)
  - Services
  - Repositories (+ RepositoryInterface/Contracts)
  - Models
  - Routes (web.php, api.php)
  - Resources/views (Blade pages, partials, components)
  - Providers ({Module}ServiceProvider)
- Advanced modules add:
  - Actions + DTOs (Trip, Spare, Admin settings)
  - Policies (Manager, Driver)
  - Events/Listeners/Jobs (Trip, Cashbook integration)
- Responsibilities by layer:
  - Controller: HTTP orchestration only
  - Request: sanitization + validation
  - Service/Action: business rules and workflow
  - Repository: query/data persistence shape
  - Provider: bindings, routes, views, listeners

## Controller Pattern
- Controllers are thin and DI services/actions via constructor property promotion.
- Repeated flow:
  - authorize/authorizeResource
  - collect filters from request
  - delegate to service/repository
  - return Blade view or JSON
- Dual response style is common for list pages:
  - full page for normal request
  - JSON with rendered partial HTML for AJAX filtering/pagination
- Mutation endpoints typically return JSON { message, ...data }.
- Redirect + flash is used more in admin/trip legacy flows.
- Guard checks use abort/abort_if/abort_unless for ownership/invariant checks.

## Service Pattern
- Services are used for business operations, invariants, and orchestration across repositories/models/events.
- Naming convention: {Domain}Service (for example ClientService, DriverService, TripService, SpareSaleService).
- Method style is verb-based and use-case oriented:
  - create/update/delete/toggleStatus
  - domain actions: requestCompletion, updateStatus, recordSale
- Dependency injection pattern:
  - interfaces in constructor when repository abstraction exists
  - concrete service/action injection for orchestration
- DB::transaction is used in multi-write workflows.
- Exceptions from service layer (RuntimeException/HttpException) are caught in controller when user-facing JSON status needs mapping.

## Validation Pattern
- Primary pattern: FormRequest classes in module Http/Requests.
- Request classes often include prepareForValidation() to trim/strip_tags input.
- Complex cross-entity checks use withValidator()->after(...).
- Inline validation still appears for small single-field updates (for example status patch).
- Validation ownership:
  - shape/type/range in FormRequest rules
  - relational/invariant checks in withValidator or service layer

## Authorization Pattern
- Layered authorization is standard:
  - route middleware: auth, verified, role:...
  - policy checks: authorizeResource, authorize('ability', $model)
  - hard guards: abort_if for direct invariant enforcement
- Policies are model-centric and role + ownership aware.
- Critical project-specific pattern:
  - effective Trip policy is App\Modules\Driver\Policies\TripPolicy via AuthServiceProvider binding.
- Gates are registered in providers using Gate::policy(...).

## Database Pattern
- Eloquent models with:
  - fillable
  - casts()
  - relationships
  - accessors/helpers for derived state
  - frequent ULID route keys in domain models
- Repository usage is common in Manager/Trip/Spare/Cashbook modules.
- Query organization patterns:
  - paginate(filters) methods in repositories
  - conditional filters via when(...)
  - eager loading with selective columns
  - withCount/withSum for dashboard/detail stats
- Service layer writes often call repository methods, but some modules still mix direct model queries in services/controllers.

## Event Pattern
- Domain events are used for cross-module side effects.
- Repeated pattern:
  - event dispatched from service/action
  - listeners registered in module provider via Event::listen
  - listener performs side effects and may dispatch queue job
- Queue usage exists where async delivery matters:
  - SendTripNotificationJob implements ShouldQueue and is dispatched by listener
- Not every flow is async; many listeners run sync and perform transactional updates.
- Payment gateway validation should verify provider-reported amounts and currency before marking a payment as succeeded.

## UI Pattern
- Blade is module-scoped using namespace views (admin::, manager::, driver::, client::, etc.).
- Layout composition pattern repeats across role modules:
  - layouts/app.blade.php
  - includes for sidebar/backdrop/header
  - @yield('content') + @stack('scripts')
- Components are reused via x-common.* (breadcrumb, card, table dropdown, theme toggle, preloader).
- Alpine.js usage pattern:
  - x-data component objects for page state
  - Alpine.store('theme') and Alpine.store('sidebar') for global UI state
  - AJAX actions in @push('scripts') blocks with fetch + DOM patch + Alpine.initTree
- Tailwind conventions:
  - utility-heavy templates with shared design tokens in resources/css/app.css
  - dark mode via .dark variant and class toggling
  - repeated utility primitives: rounded cards, gray/brand palettes, responsive grid/flex, state badges
- Toastify + SweetAlert are standard for user notifications and confirms.

## Error Handling Pattern
- Service layer throws domain exceptions (RuntimeException, HttpException) for rule violations.
- Controllers map known exceptions to JSON/HTTP responses.
- Abort helpers are used for immediate 401/403/404/422-style guards.
- Flash feedback pattern exists in redirect flows:
  - with('success') / with('error')
- Logging is selective, concentrated in sensitive operations (for example auth/profile updates) using Log::warning/Log::error in catch blocks.

## Feature Development Checklist
1. Create files in module-first layout
- Required minimum for a new CRUD-style feature:
  - app/Modules/{Module}/Models/{Entity}.php
  - app/Modules/{Module}/Http/Controllers/{Entity}Controller.php
  - app/Modules/{Module}/Http/Requests/Store{Entity}Request.php
  - app/Modules/{Module}/Http/Requests/Update{Entity}Request.php
  - app/Modules/{Module}/Repositories/{Entity}RepositoryInterface.php
  - app/Modules/{Module}/Repositories/{Entity}Repository.php
  - app/Modules/{Module}/Services/{Entity}Service.php
  - app/Modules/{Module}/Routes/web.php (and api.php if needed)
  - app/Modules/{Module}/Resources/views/pages/{entity}/... if UI exists
  - app/Modules/{Module}/Providers/{Module}ServiceProvider.php bindings/routes/views
- Add Actions/DTOs when workflow is multi-step or cross-module (Trip/Spare style).

2. Follow these implementation patterns
- Keep controllers thin; delegate to service/action.
- Put validation in FormRequest (plus prepareForValidation for sanitization).
- Enforce auth with middleware + policy + explicit guards.
- Use repository paginate(filters) for listing/filtering consistency.
- Use DB::transaction for multi-write operations.
- Return JSON for AJAX mutations and partial-refresh list endpoints.
- Emit domain events for cross-module side effects instead of direct coupling.

3. Reuse existing components first
- Reuse x-common Blade components and existing role layouts.
- Reuse toast/confirm UX (Toastify + SweetAlert) and fetch helper style.
- Reuse policy ownership rules from Manager policies and Trip policy patterns.
- Reuse repository filter/paginate shape from Manager Client/Driver/Truck and Trip repositories.
- Reuse event/listener wiring pattern from TripServiceProvider and CashbookServiceProvider.

## Notes
- Some modules are scaffold-like and partially implemented (for example Notification/Auth generic CRUD style). Prefer patterns from active modules (Manager, Driver, Trip, Spare, Cashbook, Admin) when adding new production behavior.

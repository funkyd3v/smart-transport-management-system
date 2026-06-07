# Conventions (AI Execution Notes)

## Architecture Patterns
- Prefer module-local layering: Controller -> Request/DTO -> Action/Service -> Repository -> Model.
- Keep business logic in actions/services, not in Blade or thin controllers.
- Use DI via interfaces where present (many repositories are bound in providers).

## Routing Conventions
- Role portals use prefixed route groups and named prefixes:
	- admin.*, manager.*, driver.*, client.*
- Most module routes require auth; role checks are enforced via role middleware.
- Trip module has dedicated route files under app/Modules/Trip/routes (admin.php, driver.php).

## Authorization Conventions
- Always check the effective policy binding before changing trip permissions.
- Effective Trip policy class is App\Modules\Driver\Policies\TripPolicy.
- Manager resource updates typically require ownership (created_by or approved_by linkage).

## Data + Identity Conventions
- ULID is common for public route keys in domain models.
- Two user models coexist:
	- App\Models\User used by auth provider/session auth
	- App\Modules\Auth\Models\User used in module-level creation flows
- Role checks commonly normalize role as string (supports plain string or enum-like object role storage).

## Event/Side-Effect Conventions
- Financial consistency is event-driven:
	- InvoiceGenerated creates/updates due records
	- PaymentSucceeded updates due records and trip due_amount
	- TripStatusChanged triggers notifications/tracking side effects
- Spare stock mutation side effects are observer-driven (low stock notifications).

## Frontend/Template Conventions
- Blade namespaces per module: admin::, manager::, driver::, client::, cashbook::, trip::
- Role menus are generated from app/Menu providers using route names (safeRoute fallback to #).

## Integration Conventions
- For same-session JS fetch calls in Blade, prefer relative route URLs to avoid host mismatch auth issues.
- Driver live location endpoints should be called with same-origin session assumptions.

## Guardrails For Future AI Edits
- Do not assume generic app/Policies/TripPolicy is active for trip authorization.
- When touching trip financials, verify due/invoice/payment listeners remain consistent.
- When touching spare stock fields, align reads/writes across quantity vs quantity_in_stock usages.

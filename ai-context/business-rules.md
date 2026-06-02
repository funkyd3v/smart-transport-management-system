# Critical Business Rules

## Role Access Baseline
- Admin: full control across all domains.
- Manager: operational control for owned clients/drivers/trucks/trips and finance operations on managed trips.
- Driver: can act only on assigned trips and own profile/dashboard.
- Client: dashboard-only experience in current implementation.

## Trip Lifecycle Rules
- Canonical status values: created, in_progress, completed, cancelled.
- Allowed transitions:
	- created -> created|in_progress|cancelled
	- in_progress -> in_progress|completed|cancelled
	- completed -> completed only
	- cancelled -> cancelled only
- Drivers requesting completion while in progress sets completion_requested_* fields (does not directly complete trip).
- Pending completion request blocks driver actions that mutate trip execution: location submit, add expense, add reload, status mutation from driver context.

## Trip Permission Rules
- Effective policy: App\Modules\Driver\Policies\TripPolicy.
- Driver trip access requires ownership: trip.driver_id must match driver record linked to authenticated user.
- Manager permissions for many trip operations require trip.created_by == manager user id.
- Invoice generation allowed only when trip is not already invoiced.

## Finance and Due Rules
- Initial trip due uses max(0, trip_rate - advance_payment).
- Due record is created/updated when invoice is generated.
- Payment recorded recalculates:
	- due_record.collected_amount = sum(payments)
	- due_record.remaining_due = max(0, original_due - sum(payments))
	- trip.due_amount mirrors remaining_due
- Settled flag and settled_at are set when remaining due is 0.

## Expense Rules
- Manager/admin can approve or reject trip expenses.
- Approved expense cannot be rejected; rejected expense cannot be approved.
- Trip expense totals/profit calculations rely on approved expenses.

## Driver Management Rules
- Non-admin creation/update flow should not directly set is_approved flags from request payload.
- Driver cannot be deleted while assigned to active trip.
- Manager can toggle driver status/approval only for owned drivers (policy ownership check).

## Truck/Client Ownership Rules
- Manager update/delete/status operations for trucks and clients require ownership checks.
- Admin bypasses ownership constraints.

## Spare Inventory Rules
- Spare low stock threshold constant: 3.
- Low-stock notification fires when quantity crosses from above threshold to threshold-or-below.
- Spare part sale requires sufficient stock; quantity is decremented atomically inside transaction.
- Spare sales produce cashbook credit entry.

## Operational Limits
- Global route groups commonly use throttle:60,1 in admin/driver/manager portals.
- Dedicated rate limiter trip-location allows 240 requests/minute per authenticated user/ip key.

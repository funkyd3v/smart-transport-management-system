## Trip Management

Module:
Trip

Purpose:
Manage trip lifecycle, assignment, status flow, and core trip operations.

Controllers:

* App\Modules\Manager\Http\Controllers\Trip\TripController
* App\Modules\Trip\Http\Controllers\Admin\TripController
* App\Modules\Trip\Http\Controllers\Driver\TripController

Services:

* App\Modules\Trip\Services\TripService
* App\Modules\Trip\Services\ExpenseService
* App\Modules\Trip\Services\PaymentService

Models:

* App\Modules\Trip\Models\Trip
* App\Modules\Trip\Models\TripStatus
* App\Modules\Trip\Models\TripGoods
* App\Modules\Trip\Models\TripExpense
* App\Modules\Trip\Models\ReloadHistory

Policies:

* App\Modules\Driver\Policies\TripPolicy

Views:

* manager::trips.*
* driver::pages.trips.*
* trip::driver.trips.*
* trip::admin.trips.*
* admin::pages.trips.*

Routes:

* /manager/trips/*
* /driver/trips/*
* /admin/trips/*

Related Features:

* Driver Management
* Truck Management
* Client Management
* Billing and Receivables
* Notifications and Alerts

Business Rules:

* Trip status transitions are restricted to defined lifecycle transitions.
* Drivers can operate only on assigned trips.
* Completion request blocks driver execution actions until reviewed.

## Driver Field Operations and Live Tracking

Module:
Driver

Purpose:
Enable driver-side execution of assigned trips including expense entry, reload logging, and GPS location submission.

Controllers:

* App\Modules\Driver\Http\Controllers\Trip\TripController
* App\Modules\Driver\Http\Controllers\Trip\TripExpenseController
* App\Modules\Driver\Http\Controllers\Trip\ReloadController
* App\Modules\Driver\Http\Controllers\Api\TripLocationController

Services:

* App\Modules\Driver\Services\DriverTripService
* App\Modules\Trip\Services\VehicleTrackingService

Models:

* App\Modules\Trip\Models\CurrentVehicleLocation
* App\Modules\Trip\Models\VehicleLocationHistory
* App\Modules\Trip\Models\TripExpense
* App\Modules\Trip\Models\ReloadHistory

Policies:

* App\Modules\Driver\Policies\TripPolicy

Views:

* driver::pages.trips.*
* trip::driver.trips.*

Routes:

* /driver/trips/*
* /api/driver/trips/{trip}/location

Related Features:

* Trip Management
* Billing and Receivables
* Notifications and Alerts

Business Rules:

* GPS submission requires driver role, trip assignment, and valid device/token context.
* Driver actions are blocked when completion request is pending.
* Driver cannot access trips outside ownership.

## Driver Management

Module:
Manager

Purpose:
Create, approve, activate, and maintain drivers used in trip assignment.

Controllers:

* App\Modules\Manager\Http\Controllers\Driver\DriverController
* App\Modules\Admin\Http\Controllers\DriverController

Services:

* App\Modules\Manager\Services\DriverService
* App\Modules\Admin\Services\AdminOperationsService

Models:

* App\Modules\Driver\Models\Driver
* App\Modules\Auth\Models\User

Policies:

* App\Modules\Manager\Policies\DriverPolicy

Views:

* manager::pages.drivers.*
* admin::pages.drivers.*

Routes:

* /manager/drivers/*
* /admin/drivers/*

Related Features:

* Trip Management
* User Administration and Approval

Business Rules:

* Manager can operate only on owned drivers.
* Driver approval and status gates assignment eligibility.
* Driver deletion is blocked when active trip linkage exists.

## Truck Management

Module:
Manager

Purpose:
Manage fleet records, status, and availability for trip assignment.

Controllers:

* App\Modules\Manager\Http\Controllers\Truck\TruckController
* App\Modules\Admin\Http\Controllers\TruckController

Services:

* App\Modules\Manager\Services\TruckService
* App\Modules\Admin\Services\AdminOperationsService

Models:

* App\Modules\Truck\Models\Truck
* App\Modules\Truck\Models\TruckStatus

Policies:

* App\Modules\Manager\Policies\TruckPolicy

Views:

* manager::pages.trucks.*
* admin::pages.trucks.*

Routes:

* /manager/trucks/*
* /admin/trucks/*

Related Features:

* Trip Management
* Driver Management

Business Rules:

* Manager truck updates require ownership.
* Manual status updates are restricted to allowed states.
* Trucks on active trips cannot be arbitrarily status-switched/deleted.

## Client Management

Module:
Manager

Purpose:
Manage transport clients and their operational profile used in trip creation and billing.

Controllers:

* App\Modules\Manager\Http\Controllers\Client\ClientController
* App\Modules\Admin\Http\Controllers\ClientController

Services:

* App\Modules\Manager\Services\ClientService
* App\Modules\Admin\Services\AdminOperationsService

Models:

* App\Modules\Client\Models\Client
* App\Modules\Client\Models\ClientCategory
* App\Modules\Auth\Models\User

Policies:

* App\Modules\Manager\Policies\ClientPolicy

Views:

* manager::pages.clients.*
* admin::pages.clients.*

Routes:

* /manager/clients/*
* /admin/clients/*

Related Features:

* Trip Management
* Billing and Receivables

Business Rules:

* Manager client mutation requires ownership.
* Client status controls operational availability.
* Client deletion is blocked when trip history exists.

## Billing and Receivables

Module:
Trip

Purpose:
Handle invoices, payments, due tracking, and expense approval tied to trips.

Controllers:

* App\Modules\Trip\Http\Controllers\Admin\InvoiceController
* App\Modules\Trip\Http\Controllers\Admin\PaymentController
* App\Modules\Trip\Http\Controllers\Admin\ExpenseController
* App\Modules\Manager\Http\Controllers\Trip\TripPaymentController
* App\Modules\Manager\Http\Controllers\Trip\TripExpenseApprovalController
* App\Modules\Manager\Http\Controllers\Trip\TripInvoiceController
* App\Modules\Admin\Http\Controllers\FinanceController

Services:

* App\Modules\Trip\Services\InvoiceService
* App\Modules\Trip\Services\PaymentService
* App\Modules\Trip\Services\ExpenseService
* App\Modules\Admin\Services\AdminFinanceService

Models:

* App\Modules\Trip\Models\Invoice
* App\Modules\Trip\Models\Payment
* App\Modules\Trip\Models\DueRecord
* App\Modules\Trip\Models\TripExpense

Policies:

* App\Modules\Driver\Policies\TripPolicy

Views:

* manager::trips.payments.*
* manager::trips.invoices.*
* trip::admin.payments.*
* trip::admin.invoices.*
* admin::pages.finance.*

Routes:

* /manager/trips/{trip}/payments
* /manager/trips/{trip}/invoice
* /manager/trips/{trip}/expenses/{expense}/*
* /admin/invoices/*
* /admin/trips/payments
* /admin/trips/expenses/*
* /admin/finance/*

Related Features:

* Trip Management
* Cashbook
* Reporting

Business Rules:

* Invoice generation is blocked for already invoiced trips.
* Due is recalculated from payments and synchronized to trip due amount.
* Expense approval/rejection is mutually exclusive per expense record.

## Cashbook Ledger

Module:
Cashbook

Purpose:
Maintain credit/debit ledger entries from trip and spare financial events.

Controllers:

* App\Modules\Cashbook\Controllers\CashbookController
* App\Modules\Admin\Http\Controllers\FinanceController

Services:

* App\Modules\Cashbook\Services\CashbookService

Models:

* App\Modules\Cashbook\Models\DailyCashbook

Policies:

* None (route-level auth/role middleware)

Views:

* cashbook::*
* admin::pages.finance.cashbook

Routes:

* /cashbook/*
* /api/cashbook/*
* /admin/finance/cashbook

Related Features:

* Billing and Receivables
* Spare Parts Inventory and Sales

Business Rules:

* Payment and expense events generate ledger entries.
* Spare sales record credit entries.

## Spare Parts Inventory and Sales

Module:
Spare

Purpose:
Manage spare stock, part sales, and inventory health alerts.

Controllers:

* App\Modules\Spare\Controllers\SpareController
* App\Modules\Spare\Controllers\SpareSaleController

Services:

* App\Modules\Spare\Services\SpareService
* App\Modules\Spare\Services\SpareSaleService

Models:

* App\Modules\Spare\Models\SparePart
* App\Modules\Spare\Models\SpareSale
* App\Modules\Spare\Models\SpareCategory
* App\Modules\Spare\Models\SpareSaleType

Policies:

* None (admin route protection)

Views:

* admin::pages.spare.inventory.*
* admin::pages.spare.sales.*

Routes:

* /admin/spare/inventory/*
* /admin/spare/sales/*

Related Features:

* Cashbook
* Notifications and Alerts

Business Rules:

* Stock cannot go negative during sale.
* Low-stock threshold crossing triggers admin notification.
* Sale recording updates both stock and financial ledger.

## User Administration and Approval

Module:
Admin

Purpose:
Control user lifecycle, activation, approval, and administrative account actions.

Controllers:

* App\Modules\Admin\Http\Controllers\UserController
* App\Modules\Admin\Http\Controllers\Settings\UserManagementController

Services:

* App\Modules\Admin\Services\AdminUserService
* App\Modules\Admin\Services\Settings\UserManagementService

Models:

* App\Models\User
* App\Modules\Auth\Models\User

Policies:

* App\Policies\UserPolicy

Views:

* admin::pages.users.*
* admin::pages.settings.users.*

Routes:

* /admin/users/*
* /admin/settings/users/*

Related Features:

* Authentication and Access Control
* Driver Management
* Client Management

Business Rules:

* Admin-only management for core user operations.
* Self-role/sensitive self-deactivation safeguards are enforced.
* Bulk approval and status toggles are controlled admin actions.

## Profile, Company, and Security Preferences

Module:
Admin

Purpose:
Manage authenticated user profile, password, sessions, company identity, and notification preferences.

Controllers:

* App\Modules\Admin\Http\Controllers\ProfileController
* App\Modules\Manager\Http\Controllers\ProfileController
* App\Modules\Driver\Http\Controllers\ProfileController

Services:

* App\Modules\Admin\Services\ProfileService
* App\Modules\Manager\Services\ProfileService

Models:

* App\Models\User
* App\Modules\Admin\Models\CompanySetting
* App\Modules\Admin\Models\NotificationPreference

Policies:

* None (role-gated controllers and explicit role checks)

Views:

* admin::pages.profile
* manager::pages.profile
* driver::pages.profile

Routes:

* /admin/profile/*
* /manager/profile
* /driver/profile

Related Features:

* Authentication and Access Control
* Audit and Login Monitoring

Business Rules:

* Password updates require current password verification.
* Other-device sessions can be force-terminated.
* Profile/company changes are audit logged in admin flow.

## Dashboard and KPI Analytics

Module:
Admin

Purpose:
Provide role-specific operational KPIs and snapshot metrics.

Controllers:

* App\Modules\Admin\Http\Controllers\DashboardController
* App\Modules\Manager\Http\Controllers\DashboardController
* App\Modules\Driver\Http\Controllers\DashboardController
* App\Modules\Client\Http\Controllers\DashboardController

Services:

* App\Modules\Admin\Services\AdminDashboardService
* App\Modules\Manager\Services\DashboardService

Models:

* App\Modules\Trip\Models\Trip
* App\Modules\Trip\Models\DueRecord
* App\Modules\Spare\Models\SparePart

Policies:

* None (role-gated route groups)

Views:

* admin::dashboard
* manager::dashboard
* driver::dashboard
* client::dashboard

Routes:

* /admin/dashboard
* /manager/dashboard
* /driver/dashboard
* /client/dashboard

Related Features:

* Trip Management
* Billing and Receivables
* Reporting

Business Rules:

* KPI visibility follows role route access.
* Dashboard aggregates depend on operational status and due states.

## Reporting

Module:
Admin

Purpose:
Generate and download operational and financial reports.

Controllers:

* App\Modules\Admin\Http\Controllers\ReportController

Services:

* App\Modules\Admin\Services\AdminReportService

Models:

* App\Modules\Trip\Models\Trip
* App\Modules\Trip\Models\DueRecord
* App\Modules\Trip\Models\Payment

Policies:

* None (admin route protection)

Views:

* admin::pages.reports.*

Routes:

* /admin/reports
* /admin/reports/generate
* /admin/reports/download/{type}

Related Features:

* Dashboard and KPI Analytics
* Billing and Receivables

Business Rules:

* Report generation scope is admin-controlled.
* Downloadable output type must be valid.

## Notifications and Alerts

Module:
Trip

Purpose:
Create and deliver trip/system notifications and stock/operational alerts.

Controllers:

* App\Modules\Notification\Controllers\NotificationController

Services:

* App\Modules\Notification\Services\NotificationService

Models:

* App\Modules\Trip\Models\TripNotification
* App\Modules\Notification\Models\Notification

Policies:

* None explicit (event-driven + route auth)

Views:

* admin::pages.settings.notifications.*

Routes:

* /notifications/*
* /api/notifications/*
* /admin/settings/notifications

Related Features:

* Trip Management
* Spare Parts Inventory and Sales

Business Rules:

* Trip status events trigger notification workflow.
* Low-stock transitions generate admin alerts.
* Notification sending supports asynchronous job execution.

## Audit and Login Monitoring

Module:
AuditLog

Purpose:
Track sensitive actions and authentication history for operational traceability.

Controllers:

* App\Modules\Admin\Http\Controllers\AuditLogController
* App\Modules\AuditLog\Controllers\AuditLogController

Services:

* App\Modules\AuditLog\Services\AuditLogService
* App\Modules\Auth\Services\AuthService

Models:

* App\Modules\AuditLog\Models\AuditLog
* App\Modules\Admin\Models\LoginHistory

Policies:

* None explicit (admin route protection for primary UI)

Views:

* admin::pages.audit-logs.*

Routes:

* /admin/audit-logs
* /audit-logs/*
* /api/audit-logs/*

Related Features:

* Authentication and Access Control
* Profile, Company, and Security Preferences

Business Rules:

* Login successes and failures are recorded when history table is present.
* Sensitive profile/admin operations generate audit records with actor and metadata.

## Authentication and Access Control

Module:
Auth

Purpose:
Provide login, registration, password reset, email verification, and session-based role access.

Controllers:

* App\Http\Controllers\Auth\AuthenticatedSessionController
* App\Http\Controllers\Auth\RegisteredUserController
* App\Http\Controllers\Auth\PasswordResetLinkController
* App\Http\Controllers\Auth\NewPasswordController
* App\Http\Controllers\Auth\VerifyEmailController

Services:

* App\Modules\Auth\Services\AuthService

Models:

* App\Models\User

Policies:

* App\Modules\Driver\Policies\TripPolicy
* App\Modules\Manager\Policies\ClientPolicy
* App\Modules\Manager\Policies\DriverPolicy
* App\Modules\Manager\Policies\TruckPolicy

Views:

* auth.*

Routes:

* /login
* /register
* /forgot-password
* /reset-password/*
* /verify-email/*
* /logout

Related Features:

* User Administration and Approval
* Profile, Company, and Security Preferences
* All role-based business features

Business Rules:

* Inactive users are denied active session access.
* Role middleware and policy checks gate feature access.
* Session regeneration and CSRF-safe logout are enforced.

<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use App\Models\User;
use App\Modules\AuditLog\Models\AuditLog;
use App\Modules\Cashbook\Models\DailyCashbook;
use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Due\Models\DueRecord;
use App\Modules\Payment\Models\Payment;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Models\SpareSale;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Truck\Models\Truck;
use Illuminate\Pagination\LengthAwarePaginator;

final class AdminOperationsRepository implements AdminOperationsRepositoryInterface
{
    public function usersPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->select(['id', 'ulid', 'name', 'email', 'role', 'is_active', 'last_login_at', 'created_at'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function usersStats(): array
    {
        return [
            'total' => User::query()->count(),
            'active' => User::query()->where('is_active', true)->count(),
            'inactive' => User::query()->where('is_active', false)->count(),
            'admins' => User::query()->where('role', 'admin')->count(),
        ];
    }

    public function tripsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Trip::query()
            ->select(['id', 'ulid', 'trip_code', 'client_id', 'driver_id', 'truck_id', 'status_id', 'pickup_point', 'delivery_point', 'trip_rate', 'load_date'])
            ->with([
                'client:id,user_id,company_name',
                'client.user:id,name',
                'driver:id,user_id',
                'driver.user:id,name',
                'truck:id,truck_number',
                'status:id,name',
            ])
            ->latest('id')
            ->paginate($perPage);
    }

    public function tripsStats(): array
    {
        return [
            'total' => Trip::query()->count(),
            'running' => Trip::query()->whereHas('status', fn ($q) => $q->whereIn('name', ['in_progress', 'running', 'active']))->count(),
            'completed' => Trip::query()->whereHas('status', fn ($q) => $q->where('name', 'completed'))->count(),
            'today' => Trip::query()->whereDate('created_at', now()->toDateString())->count(),
        ];
    }

    public function driversPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Driver::query()
            ->select(['id', 'ulid', 'user_id', 'license_number', 'driving_type', 'is_available', 'rating', 'total_trips'])
            ->with('user:id,name,email,is_active')
            ->latest('id')
            ->paginate($perPage);
    }

    public function driversStats(): array
    {
        return [
            'total' => Driver::query()->count(),
            'available' => Driver::query()->where('is_available', true)->count(),
            'busy' => Driver::query()->where('is_available', false)->count(),
            'avg_rating' => round((float) Driver::query()->avg('rating'), 2),
        ];
    }

    public function trucksPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Truck::query()
            ->select(['id', 'ulid', 'truck_number', 'model', 'brand', 'year', 'capacity_tons', 'status_id', 'current_driver_id'])
            ->with(['status:id,name', 'currentDriver:id,user_id', 'currentDriver.user:id,name'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function trucksStats(): array
    {
        return [
            'total' => Truck::query()->count(),
            'workshop' => Truck::query()->whereHas('status', fn ($q) => $q->whereIn('name', ['workshop', 'under_workshop', 'under maintenance']))->count(),
            'with_driver' => Truck::query()->whereNotNull('current_driver_id')->count(),
            'new_this_month' => Truck::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];
    }

    public function clientsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Client::query()
            ->select(['id', 'ulid', 'user_id', 'category_id', 'company_name', 'project_name', 'total_business_amount', 'total_due'])
            ->with(['user:id,name,email', 'category:id,name'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function clientsStats(): array
    {
        return [
            'total' => Client::query()->count(),
            'with_due' => Client::query()->where('total_due', '>', 0)->count(),
            'business' => (float) Client::query()->sum('total_business_amount'),
            'due' => (float) Client::query()->sum('total_due'),
        ];
    }

    public function duesPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return DueRecord::query()
            ->select(['id', 'ulid', 'trip_id', 'client_id', 'original_due', 'collected_amount', 'remaining_due', 'due_date', 'is_settled'])
            ->with([
                'trip:id,trip_code',
                'client:id,user_id,company_name',
                'client.user:id,name',
            ])
            ->orderByDesc('remaining_due')
            ->paginate($perPage);
    }

    public function duesStats(): array
    {
        return [
            'outstanding' => (float) DueRecord::query()->sum('remaining_due'),
            'collected' => (float) DueRecord::query()->sum('collected_amount'),
            'open_records' => DueRecord::query()->where('is_settled', false)->count(),
            'settled_records' => DueRecord::query()->where('is_settled', true)->count(),
        ];
    }

    public function cashbookPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return DailyCashbook::query()
            ->select(['id', 'ulid', 'entry_date', 'recorded_by', 'total_income', 'total_expense', 'net_profit', 'closing_balance', 'is_finalized'])
            ->with('recordedBy:id,name')
            ->latest('entry_date')
            ->paginate($perPage);
    }

    public function cashbookStats(): array
    {
        return [
            'income_this_month' => (float) DailyCashbook::query()->whereMonth('entry_date', now()->month)->whereYear('entry_date', now()->year)->sum('total_income'),
            'expense_this_month' => (float) DailyCashbook::query()->whereMonth('entry_date', now()->month)->whereYear('entry_date', now()->year)->sum('total_expense'),
            'profit_this_month' => (float) DailyCashbook::query()->whereMonth('entry_date', now()->month)->whereYear('entry_date', now()->year)->sum('net_profit'),
            'open_days' => DailyCashbook::query()->where('is_finalized', false)->count(),
        ];
    }

    public function sparePartsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return SparePart::query()
            ->select(['id', 'ulid', 'category_id', 'part_name', 'condition', 'sourced_from_truck_id', 'quantity_in_stock', 'purchase_price'])
            ->with(['category:id,name', 'sourcedFromTruck:id,truck_number'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function sparePartsStats(): array
    {
        return [
            'total_parts' => SparePart::query()->count(),
            'low_stock' => SparePart::query()->where('quantity_in_stock', '<=', 5)->count(),
            'inventory_value' => (float) SparePart::query()->selectRaw('SUM(quantity_in_stock * purchase_price) as total')->value('total'),
            'sold_items' => (int) SpareSale::query()->sum('quantity_sold'),
        ];
    }

    public function spareSalesPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return SpareSale::query()
            ->select(['id', 'ulid', 'spare_part_id', 'sale_type_id', 'sold_by', 'buyer_name', 'quantity_sold', 'sale_price', 'profit', 'sale_date'])
            ->with(['sparePart:id,part_name', 'saleType:id,name', 'soldBy:id,name'])
            ->latest('sale_date')
            ->paginate($perPage);
    }

    public function spareSalesStats(): array
    {
        return [
            'revenue' => (float) SpareSale::query()->sum('sale_price'),
            'profit' => (float) SpareSale::query()->sum('profit'),
            'transactions' => SpareSale::query()->count(),
            'today_sales' => SpareSale::query()->whereDate('sale_date', now()->toDateString())->count(),
        ];
    }

    public function auditLogsPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return AuditLog::query()
            ->select(['id', 'user_id', 'action', 'table_name', 'record_id', 'ip_address', 'created_at'])
            ->with('user:id,name,role')
            ->latest('id')
            ->paginate($perPage);
    }

    public function reportStats(): array
    {
        return [
            'trips' => Trip::query()->count(),
            'payments' => (float) Payment::query()->sum('amount'),
            'expenses' => (float) TripExpense::query()->sum('amount'),
            'dues' => (float) DueRecord::query()->sum('remaining_due'),
        ];
    }

    public function settingsSnapshot(): array
    {
        return [
            'app_name' => (string) config('app.name'),
            'app_env' => (string) config('app.env'),
            'app_url' => (string) config('app.url'),
            'timezone' => (string) config('app.timezone'),
            'locale' => (string) config('app.locale'),
            'mail_driver' => (string) config('mail.default'),
            'queue_driver' => (string) config('queue.default'),
        ];
    }
}

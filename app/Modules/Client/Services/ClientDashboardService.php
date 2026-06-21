<?php

declare(strict_types=1);

namespace App\Modules\Client\Services;

use App\Modules\Client\Models\Client;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Payment\Models\Payment;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Carbon;

class ClientDashboardService
{
    public function getDashboardData(Client $client): array
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfDay();

        $activeTrips = Trip::with(['truck', 'driver.user', 'status', 'currentVehicleLocation'])
            ->where('client_id', $client->id)
            ->whereHas('status', fn ($q) => $q->where('name', TripStatus::InProgress->value))
            ->orderByDesc('load_date')
            ->get();

        $recentTrips = Trip::with(['truck', 'status', 'invoice', 'dueRecord'])
            ->where('client_id', $client->id)
            ->whereHas('status', fn ($q) => $q->whereIn('name', [
                TripStatus::Completed->value,
                TripStatus::Cancelled->value,
            ]))
            ->where('updated_at', '>=', $sixMonthsAgo)
            ->orderByDesc('completed_at')
            ->limit(10)
            ->get();

        $outstandingDues = $client->dueRecords()
            ->with(['trip:id,trip_code,pickup_point,delivery_point,ulid'])
            ->where('is_settled', false)
            ->orderBy('due_date')
            ->get();

        $recentPayments = $client->payments()
            ->with(['trip:id,trip_code'])
            ->where('status', 'succeeded')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->orderByDesc('payment_date')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::with(['trip:id,trip_code,ulid'])
            ->where('client_id', $client->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->orderByDesc('issued_at')
            ->limit(5)
            ->get();

        $totalOutstanding = $outstandingDues->sum('remaining_due');
        $overdueCount = $outstandingDues->filter(fn ($r) => $r->due_date && $r->due_date->isPast())->count();

        $completedThisMonth = Trip::where('client_id', $client->id)
            ->whereHas('status', fn ($q) => $q->where('name', TripStatus::Completed->value))
            ->whereBetween('completed_at', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->count();

        $totalPaidSixMonths = $client->payments()
            ->where('status', 'succeeded')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->sum('amount');

        return [
            'activeTrips'       => $activeTrips,
            'recentTrips'       => $recentTrips,
            'outstandingDues'   => $outstandingDues,
            'recentPayments'    => $recentPayments,
            'recentInvoices'    => $recentInvoices,
            'totalOutstanding'  => (float) $totalOutstanding,
            'overdueCount'      => $overdueCount,
            'completedThisMonth'=> $completedThisMonth,
            'totalPaidSixMonths'=> (float) $totalPaidSixMonths,
            'activeCount'       => $activeTrips->count(),
            'unsettledCount'    => $outstandingDues->count(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Manager\Services;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
use App\Modules\Trip\Models\Trip;
use App\Modules\Truck\Models\Truck;
use Illuminate\Support\Collection;

final class ProfileService
{
    /**
     * @return array<string, int|float>
     */
    public function managerStats(User $manager): array
    {
        $managerId = (int) $manager->id;

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $managedClients = Client::query()
            ->where('created_by', $managerId)
            ->count();

        $managedDrivers = Driver::query()
            ->where('created_by', $managerId)
            ->count();

        $managedTrucks = Truck::query()
            ->where('created_by', $managerId)
            ->count();

        $activeTrips = Trip::query()
            ->where('created_by', $managerId)
            ->whereHas('status', fn ($query) => $query->whereIn('name', [
                TripStatusEnum::Created->value,
                TripStatusEnum::InProgress->value,
                'active',
                'in_transit',
            ]))
            ->count();

        $completedThisMonth = Trip::query()
            ->where('created_by', $managerId)
            ->whereHas('status', fn ($query) => $query->where('name', TripStatusEnum::Completed->value))
            ->whereBetween('load_date', [$monthStart, $monthEnd])
            ->count();

        $monthRevenue = (float) Trip::query()
            ->where('created_by', $managerId)
            ->whereBetween('load_date', [$monthStart, $monthEnd])
            ->sum('total_income');

        $outstandingDue = (float) Trip::query()
            ->where('created_by', $managerId)
            ->where('due_amount', '>', 0)
            ->sum('due_amount');

        return [
            'managed_clients' => $managedClients,
            'managed_drivers' => $managedDrivers,
            'managed_trucks' => $managedTrucks,
            'active_trips' => $activeTrips,
            'completed_this_month' => $completedThisMonth,
            'month_revenue' => $monthRevenue,
            'outstanding_due' => $outstandingDue,
        ];
    }

    public function recentTrips(User $manager, int $limit = 5): Collection
    {
        return Trip::query()
            ->where('created_by', (int) $manager->id)
            ->with([
                'status:id,name',
                'client:id,company_name',
                'truck:id,truck_number',
            ])
            ->latest('load_date')
            ->limit($limit)
            ->get();
    }

    public function profileCompletion(User $manager): int
    {
        $checkpoints = [
            filled($manager->name),
            filled($manager->email),
            filled($manager->phone),
            $manager->email_verified_at !== null,
            filled($manager->last_login_at),
        ];

        $completed = count(array_filter($checkpoints));

        return (int) round(($completed / max(1, count($checkpoints))) * 100);
    }
}

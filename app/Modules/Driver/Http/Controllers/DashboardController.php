<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $driver = Driver::query()
            ->with('user')
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $driver) {
            return view('driver::dashboard', [
                'driver' => null,
                'activeTrip' => null,
                'recentTrips' => collect(),
                'stats' => ['completed' => 0, 'this_month' => 0, 'total' => 0],
            ]);
        }

        $activeTrip = Trip::query()
            ->with(['truck:id,truck_number,model', 'client:id,company_name,user_id', 'client.user:id,name,phone', 'status:id,name', 'goods'])
            ->where('driver_id', $driver->id)
            ->whereHas('status', fn ($q) => $q->whereIn('name', [TripStatus::Created->value, TripStatus::InProgress->value]))
            ->latest('load_date')
            ->first();

        $recentTrips = Trip::query()
            ->select(['id', 'ulid', 'trip_code', 'pickup_point', 'delivery_point', 'load_date', 'status_id', 'truck_id'])
            ->with(['status:id,name', 'truck:id,truck_number'])
            ->where('driver_id', $driver->id)
            ->latest('load_date')
            ->limit(5)
            ->get();

        $stats = [
            'completed' => Trip::query()
                ->where('driver_id', $driver->id)
                ->whereHas('status', fn ($q) => $q->where('name', TripStatus::Completed->value))
                ->count(),
            'this_month' => Trip::query()
                ->where('driver_id', $driver->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total' => Trip::query()
                ->where('driver_id', $driver->id)
                ->count(),
        ];

        return view('driver::dashboard', compact('driver', 'activeTrip', 'recentTrips', 'stats'));
    }
}

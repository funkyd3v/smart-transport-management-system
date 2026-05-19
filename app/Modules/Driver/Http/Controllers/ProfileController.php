<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\UpdateProfileRequest;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $driver = Driver::query()
            ->with('user')
            ->where('user_id', $user->id)
            ->first();

        $stats = [
            'total' => 0,
            'completed' => 0,
            'this_month' => 0,
        ];

        $recentTrips = collect();

        if ($driver) {
            $stats['total'] = Trip::query()->where('driver_id', $driver->id)->count();
            $stats['completed'] = Trip::query()
                ->where('driver_id', $driver->id)
                ->whereHas('status', fn ($q) => $q->where('name', TripStatus::Completed->value))
                ->count();
            $stats['this_month'] = Trip::query()
                ->where('driver_id', $driver->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $recentTrips = Trip::query()
                ->where('driver_id', $driver->id)
                ->with([
                    'status:id,name',
                    'client:id,company_name',
                    'truck:id,truck_number',
                ])
                ->latest('load_date')
                ->limit(5)
                ->get();
        }

        return view('driver::pages.profile', compact('user', 'driver', 'stats', 'recentTrips'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->forceFill([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ])->save();

        if ($request->hasFile('avatar')) {
            $user
                ->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');

            $user->refresh();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully.',
                'user' => [
                    'name' => (string) $user->name,
                    'phone' => (string) $user->phone,
                    'email' => (string) $user->email,
                    'avatar_url' => (string) $user->avatar_url,
                ],
            ]);
        }

        return redirect()->route('driver.profile')->with('success', 'Profile updated successfully.');
    }
}

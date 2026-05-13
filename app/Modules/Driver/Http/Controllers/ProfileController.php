<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\UpdateProfileRequest;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Contracts\View\View;
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
        }

        return view('driver::pages.profile', compact('user', 'driver', 'stats'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill([
            'name' => $request->validated()['name'],
        ])->save();

        $phone = $request->validated()['phone'] ?? null;

        if ($phone !== null) {
            Driver::query()
                ->where('user_id', $user->id)
                ->update(['mobile_number' => $phone]);
        }

        return redirect()->route('driver.profile')->with('success', 'Profile updated successfully.');
    }
}

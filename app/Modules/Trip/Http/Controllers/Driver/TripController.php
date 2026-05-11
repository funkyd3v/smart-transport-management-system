<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Actions\AddReloadHistoryAction;
use App\Modules\Trip\Actions\UpdateTripStatusAction;
use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Http\Requests\UpdateTripStatusRequest;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\View\View;

final class TripController extends Controller
{
    public function __construct(
        private readonly UpdateTripStatusAction $updateTripStatus,
        private readonly AddReloadHistoryAction $addReloadHistory,
    ) {}

    public function index(Request $request): View
    {
        $driverId = Driver::query()->where('user_id', $request->user()->id)->value('id');

        if (! $driverId) {
            $trips = new ConcretePaginator([], 0, 20);

            return view('trip::driver.trips.index', compact('trips'));
        }

        $trips = Trip::query()
            ->where('driver_id', $driverId)
            ->whereHas('status', fn ($query) => $query->whereIn('name', [TripStatus::Created->value, TripStatus::InProgress->value]))
            ->latest('id')
            ->paginate(20);

        return view('trip::driver.trips.index', compact('trips'));
    }

    public function show(string $tripUlid, Request $request): View
    {
        $driverId = Driver::query()->where('user_id', $request->user()->id)->value('id');
        abort_unless((bool) $driverId, 404);

        $trip = Trip::query()->where('ulid', $tripUlid)->where('driver_id', $driverId)->firstOrFail();
        $this->authorize('view', $trip);

        return view('trip::driver.trips.show', compact('trip'));
    }

    public function updateStatus(UpdateTripStatusRequest $request): RedirectResponse
    {
        $dto = UpdateTripStatusDTO::fromRequest($request);
        ($this->updateTripStatus)($dto);

        return back()->with('success', 'Trip status updated.');
    }

    public function reload(string $tripUlid, Request $request): RedirectResponse
    {
        $driverId = Driver::query()->where('user_id', $request->user()->id)->value('id');
        abort_unless((bool) $driverId, 404);

        $trip = Trip::query()->where('ulid', $tripUlid)->where('driver_id', $driverId)->firstOrFail();
        $this->authorize('addReloadHistory', $trip);
        ($this->addReloadHistory)($trip, (int) $trip->truck_id, (int) $trip->driver_id, $request->string('reload_point')->toString(), $request->string('note')->toString());

        return back()->with('success', 'Reload history added.');
    }
}

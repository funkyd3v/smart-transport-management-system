<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Actions\CreateTripAction;
use App\Modules\Trip\Actions\UpdateTripStatusAction;
use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Http\Requests\CreateTripRequest;
use App\Modules\Trip\Http\Requests\UpdateTripStatusRequest;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use App\Modules\Truck\Models\Truck;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TripController extends Controller
{
    public function __construct(
        private readonly CreateTripAction $createTrip,
        private readonly UpdateTripStatusAction $updateTripStatus,
        private readonly TripRepositoryInterface $tripRepository,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Trip::class);

        $filters = $request->only(['status_id', 'client_id', 'driver_id', 'date_from', 'date_to']);
        $trips = $this->tripRepository->paginate($filters);

        return view('manager::trips.index', [
            'trips' => $trips,
            'statuses' => TripStatus::query()->orderBy('name')->get(),
            'clients' => Client::query()->with('user')->orderBy('id')->get(),
            'drivers' => Driver::query()->with('user')->orderBy('id')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Trip::class);

        return view('manager::trips.create', [
            'statuses' => TripStatus::query()->orderBy('name')->get(),
            'clients' => Client::query()->with('user')->orderBy('id')->get(),
            'drivers' => Driver::query()->with('user')->orderBy('id')->get(),
            'trucks' => Truck::query()->orderBy('truck_number')->get(),
        ]);
    }

    public function store(CreateTripRequest $request): RedirectResponse
    {
        $this->authorize('create', Trip::class);

        $dto = CreateTripDTO::fromRequest($request);
        $trip = ($this->createTrip)($dto);

        return redirect()->route('manager.trips.show', $trip->ulid)->with('success', 'Trip created successfully.');
    }

    public function show(string $trip): View
    {
        $tripModel = $this->tripRepository->findByUlid($trip);
        $this->authorize('view', $tripModel);

        return view('manager::trips.show', ['trip' => $tripModel]);
    }

    public function updateStatus(UpdateTripStatusRequest $request): RedirectResponse
    {
        $trip = $this->tripRepository->findByUlid((string) $request->validated()['trip_ulid']);
        $this->authorize('update', $trip);

        $dto = UpdateTripStatusDTO::fromRequest($request);
        ($this->updateTripStatus)($dto);

        return back()->with('success', 'Trip status updated successfully.');
    }
}

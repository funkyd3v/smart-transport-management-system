<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Trip\Actions\CreateTripAction;
use App\Modules\Trip\Actions\UpdateTripStatusAction;
use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Http\Requests\CreateTripRequest;
use App\Modules\Trip\Http\Requests\UpdateTripStatusRequest;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TripController extends Controller
{
    public function __construct(
        private readonly AdminOperationsService $adminService,
        private readonly CreateTripAction $createTrip,
        private readonly UpdateTripStatusAction $updateTripStatus,
        private readonly TripRepositoryInterface $tripRepository,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Trip::class);

        return view('admin::pages.trips.index', $this->adminService->tripsPageData());
    }

    public function create(): View
    {
        $this->authorize('create', Trip::class);

        return view('trip::admin.trips.create');
    }

    public function store(CreateTripRequest $request): RedirectResponse
    {
        $this->authorize('create', Trip::class);

        $dto = CreateTripDTO::fromRequest($request);
        $trip = ($this->createTrip)($dto);

        return redirect()->route('admin.trips.show', $trip->ulid)->with('success', 'Trip created successfully.');
    }

    public function show(string $trip): View
    {
        $tripModel = $this->tripRepository->findByUlid($trip);
        $this->authorize('view', $tripModel);

        return view('trip::admin.trips.show', ['trip' => $tripModel]);
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

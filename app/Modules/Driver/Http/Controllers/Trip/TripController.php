<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Driver\Http\Requests\Trip\UpdateTripStatusRequest;
use App\Modules\Driver\Models\Driver;
use App\Modules\Driver\Repositories\Trip\DriverTripRepositoryInterface;
use App\Modules\Driver\Services\DriverTripService;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TripController extends Controller
{
    public function __construct(
        private readonly DriverTripService $driverTripService,
        private readonly DriverTripRepositoryInterface $tripRepository,
    ) {}

    public function index(Request $request): View
    {
        $driver = $this->resolveDriver($request->user());
        $filters = [
            'status' => $request->string('status')->toString(),
        ];

        $trips = $this->driverTripService->getDriverTrips($driver, $filters);

        return view('driver::pages.trips.index', [
            'trips' => $trips,
            'filters' => $filters,
        ]);
    }

    public function show(Trip $trip, Request $request): View
    {
        $driver = $this->resolveDriver($request->user());
        $ownedTrip = $this->tripRepository->findByIdForDriver((int) $trip->id, (int) $driver->id);
        $this->authorize('view', $ownedTrip);

        $tripDetail = $this->driverTripService->getTripDetail($ownedTrip);

        return view('driver::pages.trips.show', [
            'trip' => $tripDetail,
            'summary' => $this->driverTripService->getFinancialSummary($tripDetail),
        ]);
    }

    public function updateStatus(UpdateTripStatusRequest $request, Trip $trip): JsonResponse
    {
        $driver = $this->resolveDriver($request->user());
        $ownedTrip = $this->tripRepository->findByIdForDriver((int) $trip->id, (int) $driver->id);
        $this->authorize('updateStatus', $ownedTrip);

        $status = TripStatus::from((string) $request->validated()['status']);
        $updatedTrip = $status === TripStatus::Completed
            ? $this->driverTripService->completeTrip($ownedTrip)
            : $this->driverTripService->startTrip($ownedTrip);

        return response()->json([
            'message' => $status === TripStatus::Completed
                ? 'Trip completed successfully.'
                : 'Trip started successfully.',
            'trip' => [
                'status' => $status->value,
                'status_label' => $status->label(),
                'badge_class' => $this->badgeClass($status->value),
            ],
        ]);
    }

    private function resolveDriver(?User $user): Driver
    {
        abort_unless($user instanceof User, 401);

        return Driver::query()->where('user_id', $user->id)->firstOrFail();
    }

    private function badgeClass(string $status): string
    {
        return match ($status) {
            TripStatus::Created->value => 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-300',
            TripStatus::InProgress->value => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
            TripStatus::Completed->value => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
            TripStatus::Cancelled->value => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-300',
        };
    }
}

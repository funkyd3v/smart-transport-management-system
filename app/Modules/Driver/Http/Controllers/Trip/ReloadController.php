<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Driver\Http\Requests\Trip\AddReloadRequest;
use App\Modules\Driver\Models\Driver;
use App\Modules\Driver\Repositories\Trip\DriverTripRepositoryInterface;
use App\Modules\Driver\Services\DriverTripService;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\JsonResponse;

final class ReloadController extends Controller
{
    public function __construct(
        private readonly DriverTripService $driverTripService,
        private readonly DriverTripRepositoryInterface $tripRepository,
    ) {}

    public function store(AddReloadRequest $request, Trip $trip): JsonResponse
    {
        $driver = $this->resolveDriver($request->user());
        $ownedTrip = $this->tripRepository->findByIdForDriver((int) $trip->id, (int) $driver->id);
        $this->authorize('addReload', $ownedTrip);

        $reload = $this->driverTripService->addReload($ownedTrip, $request->validated());

        return response()->json([
            'message' => 'Reload history added successfully.',
            'reload' => [
                'id' => $reload->id,
                'location' => (string) ($reload->reload_point ?? ''),
                'reload_amount' => (float) ($reload->reload_amount ?? 0),
                'note' => (string) ($reload->note_text ?? ''),
                'reloaded_at' => optional($reload->reloaded_at)->format('Y-m-d H:i'),
            ],
        ]);
    }

    private function resolveDriver(?User $user): Driver
    {
        abort_unless($user instanceof User, 401);

        return Driver::query()->where('user_id', $user->id)->firstOrFail();
    }
}

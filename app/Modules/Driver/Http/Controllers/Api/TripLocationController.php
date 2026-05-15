<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Driver\Http\Requests\Trip\StoreTripLocationRequest;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\DTOs\UpsertVehicleLocationDTO;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\VehicleTrackingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

final class TripLocationController extends Controller
{
    public function __construct(private readonly VehicleTrackingService $vehicleTrackingService) {}

    public function store(StoreTripLocationRequest $request, Trip $trip): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $driver = Driver::query()->where('user_id', $user->id)->firstOrFail();

        if ((int) $trip->driver_id !== (int) $driver->id) {
            abort(403, 'You are not assigned to this trip.');
        }

        $this->authorize('submitLocation', $trip);

        $deviceId = (string) $request->validated()['device_id'];
        $tokenName = (string) ($user->currentAccessToken()?->name ?? '');

        if ($tokenName !== '' && $tokenName !== $deviceId) {
            abort(403, 'Token device mismatch.');
        }

        $validated = $request->validated();

        $dto = new UpsertVehicleLocationDTO(
            tripId: (int) $trip->id,
            tripUlid: (string) $trip->ulid,
            driverId: (int) $driver->id,
            truckId: (int) $trip->truck_id,
            latitude: (float) $validated['latitude'],
            longitude: (float) $validated['longitude'],
            accuracyMeters: isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
            speedKph: isset($validated['speed_kph']) ? (float) $validated['speed_kph'] : null,
            headingDegrees: isset($validated['heading_degrees']) ? (int) $validated['heading_degrees'] : null,
            capturedAt: CarbonImmutable::parse((string) $validated['captured_at']),
            receivedAt: CarbonImmutable::now(),
            source: (string) ($validated['source'] ?? 'driver_device'),
        );

        $result = $this->vehicleTrackingService->ingest($dto);

        return response()->json([
            'accepted' => $result->accepted,
            'broadcasted' => $result->broadcasted,
            'history_stored' => $result->historyStored,
            'message' => $result->message,
        ], $result->accepted ? 200 : 202);
    }
}

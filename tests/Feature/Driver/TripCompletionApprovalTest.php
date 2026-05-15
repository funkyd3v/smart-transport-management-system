<?php

declare(strict_types=1);

namespace Tests\Feature\Driver;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripCompletionApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_completion_request_keeps_trip_in_progress_and_blocks_location_updates(): void
    {
        [$manager, $driverUser, $trip] = $this->buildTripWithStatus(TripStatusEnum::InProgress->value);

        $this->actingAs($driverUser);

        $response = $this->patchJson(route('driver.trips.update-status', $trip), [
            'status' => TripStatusEnum::Completed->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Trip marked complete and sent to manager/admin for approval.')
            ->assertJsonPath('trip.status', TripStatusEnum::InProgress->value);

        $trip->refresh();

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status_id' => $trip->status_id,
            'completed_at' => null,
            'completion_requested_by' => $driverUser->id,
        ]);

        Sanctum::actingAs($driverUser, ['*']);

        $locationResponse = $this->postJson(route('driver.api.trips.location.store', $trip), [
            'latitude' => 23.780887,
            'longitude' => 90.279237,
            'captured_at' => now()->toIso8601String(),
            'device_id' => 'TestToken',
        ]);

        $locationResponse->assertForbidden();
    }

    public function test_manager_approval_finalizes_requested_completion(): void
    {
        [$manager, $driverUser, $trip] = $this->buildTripWithStatus(TripStatusEnum::InProgress->value);

        $this->actingAs($driverUser);

        $this->patchJson(route('driver.trips.update-status', $trip), [
            'status' => TripStatusEnum::Completed->value,
        ])->assertOk();

        $this->actingAs($manager);

        $response = $this->patchJson(route('manager.trips.update-status', $trip), [
            'status' => TripStatusEnum::Completed->value,
            'note' => 'Approved after review.',
        ]);

        $response->assertOk();

        $trip->refresh();

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'completion_requested_at' => null,
        ]);

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'completed_at' => $trip->completed_at,
        ]);
    }

    /**
     * @return array{0: User, 1: User, 2: Trip}
     */
    private function buildTripWithStatus(string $status): array
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $driverUser = User::factory()->create(['role' => 'driver']);
        $clientUser = User::factory()->create(['role' => 'client']);

        $category = ClientCategory::query()->create(['name' => 'General']);
        $client = Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'user_id' => $clientUser->id,
            'created_by' => $manager->id,
            'category_id' => $category->id,
            'company_name' => 'Client Co',
        ]);

        $driver = Driver::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'user_id' => $driverUser->id,
            'license_number' => 'LIC-'.Str::upper(Str::random(8)),
            'nid_number' => 'NID-'.Str::upper(Str::random(8)),
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ]);

        $truckStatus = TruckStatus::query()->firstOrCreate(['name' => 'Idle']);
        $truck = Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'truck_number' => 'TRK-'.Str::upper(Str::random(5)),
            'model' => 'Model X',
            'status_id' => $truckStatus->id,
        ]);

        $tripStatus = TripStatus::query()->firstOrCreate(['name' => $status]);

        $trip = Trip::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_code' => 'TRIP-'.Str::upper(Str::random(6)),
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'created_by' => $manager->id,
            'status_id' => $tripStatus->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now(),
            'trip_rate' => 25000,
            'total_income' => 25000,
            'total_expense' => 0,
            'due_amount' => 0,
            'profit' => 25000,
        ]);

        return [$manager, $driverUser, $trip];
    }
}

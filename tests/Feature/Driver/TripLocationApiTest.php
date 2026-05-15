<?php

declare(strict_types=1);

namespace Tests\Feature\Driver;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
use App\Modules\Trip\Events\TripStatusChanged;
use App\Modules\Trip\Models\CurrentVehicleLocation;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripLocationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_submit_location_for_assigned_in_progress_trip(): void
    {
        [$driverUser, $trip] = $this->buildTripWithStatus(TripStatusEnum::InProgress->value);

        Sanctum::actingAs($driverUser, ['*']);

        $response = $this->postJson(route('driver.api.trips.location.store', $trip), [
            'latitude' => 23.780887,
            'longitude' => 90.279237,
            'accuracy_meters' => 12.5,
            'speed_kph' => 35.2,
            'heading_degrees' => 125,
            'captured_at' => now()->toIso8601String(),
            'device_id' => 'TestToken',
            'source' => 'test_device',
        ]);

        $response->assertOk()
            ->assertJsonPath('accepted', true);

        $this->assertDatabaseHas('current_vehicle_locations', [
            'trip_id' => $trip->id,
            'driver_id' => $trip->driver_id,
            'source' => 'test_device',
            'is_online' => true,
        ]);

        $this->assertDatabaseHas('vehicle_location_histories', [
            'trip_id' => $trip->id,
            'driver_id' => $trip->driver_id,
            'source' => 'test_device',
        ]);
    }

    public function test_location_submission_is_blocked_for_completed_trip(): void
    {
        [$driverUser, $trip] = $this->buildTripWithStatus(TripStatusEnum::Completed->value);

        Sanctum::actingAs($driverUser, ['*']);

        $response = $this->postJson(route('driver.api.trips.location.store', $trip), [
            'latitude' => 23.780887,
            'longitude' => 90.279237,
            'captured_at' => now()->toIso8601String(),
            'device_id' => 'TestToken',
        ]);

        $response->assertForbidden();
    }

    public function test_trip_status_changed_event_marks_current_location_offline(): void
    {
        [$driverUser, $trip] = $this->buildTripWithStatus(TripStatusEnum::InProgress->value);

        CurrentVehicleLocation::query()->create([
            'trip_id' => $trip->id,
            'driver_id' => $trip->driver_id,
            'truck_id' => $trip->truck_id,
            'latitude' => 23.780887,
            'longitude' => 90.279237,
            'captured_at' => now()->subSeconds(10),
            'received_at' => now()->subSeconds(10),
            'is_online' => true,
            'source' => 'test_device',
        ]);

        event(new TripStatusChanged($trip, TripStatusEnum::InProgress, TripStatusEnum::Completed));

        $this->assertDatabaseHas('current_vehicle_locations', [
            'trip_id' => $trip->id,
            'is_online' => false,
        ]);

    }

    /**
     * @return array{0: User, 1: Trip}
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

        return [$driverUser, $trip];
    }
}

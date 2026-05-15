<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Tests\TestCase;

class ManagerOwnershipPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_others_records_but_cannot_mutate_them(): void
    {
        $managerA = User::factory()->create(['role' => 'manager']);
        $managerB = User::factory()->create(['role' => 'manager']);

        $clientCategory = ClientCategory::query()->create(['name' => 'Port']);
        $truckStatus = TruckStatus::query()->create(['name' => 'Idle']);
        $tripStatus = TripStatus::query()->create(['name' => 'created']);

        $clientOwner = User::factory()->create(['role' => 'client']);
        $client = Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $managerA->id,
            'user_id' => $clientOwner->id,
            'category_id' => $clientCategory->id,
            'company_name' => 'ACME Client',
        ]);

        $driverOwner = User::factory()->create(['role' => 'driver']);
        $driver = Driver::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $managerA->id,
            'user_id' => $driverOwner->id,
            'license_number' => 'LIC-1001',
            'nid_number' => 'NID-1001',
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ]);

        $truck = Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $managerA->id,
            'truck_number' => 'TRK-1001',
            'model' => 'Model A',
            'status_id' => $truckStatus->id,
        ]);

        $trip = Trip::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_code' => 'T-1001',
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'created_by' => $managerA->id,
            'status_id' => $tripStatus->id,
            'pickup_point' => 'A',
            'delivery_point' => 'B',
            'load_date' => now(),
            'trip_rate' => 1000,
            'total_income' => 1000,
        ]);

        self::assertTrue(Gate::forUser($managerB)->allows('view', $client));
        self::assertTrue(Gate::forUser($managerB)->allows('view', $driver));
        self::assertTrue(Gate::forUser($managerB)->allows('view', $truck));
        self::assertTrue(Gate::forUser($managerB)->allows('view', $trip));

        self::assertFalse(Gate::forUser($managerB)->allows('update', $client));
        self::assertFalse(Gate::forUser($managerB)->allows('delete', $client));
        self::assertFalse(Gate::forUser($managerB)->allows('toggleStatus', $client));

        self::assertFalse(Gate::forUser($managerB)->allows('update', $driver));
        self::assertFalse(Gate::forUser($managerB)->allows('delete', $driver));
        self::assertFalse(Gate::forUser($managerB)->allows('toggleStatus', $driver));
        self::assertFalse(Gate::forUser($managerB)->allows('toggleApproval', $driver));

        self::assertFalse(Gate::forUser($managerB)->allows('update', $truck));
        self::assertFalse(Gate::forUser($managerB)->allows('delete', $truck));
        self::assertFalse(Gate::forUser($managerB)->allows('updateStatus', $truck));

        self::assertFalse(Gate::forUser($managerB)->allows('updateStatus', $trip));
        self::assertFalse(Gate::forUser($managerB)->allows('recordExpense', $trip));
        self::assertFalse(Gate::forUser($managerB)->allows('recordPayment', $trip));
        self::assertFalse(Gate::forUser($managerB)->allows('generateInvoice', $trip));
    }

    public function test_owner_manager_can_mutate_owned_records(): void
    {
        $managerA = User::factory()->create(['role' => 'manager']);
        $clientCategory = ClientCategory::query()->create(['name' => 'Port']);
        $truckStatus = TruckStatus::query()->create(['name' => 'Idle']);
        $tripStatus = TripStatus::query()->create(['name' => 'created']);

        $clientOwner = User::factory()->create(['role' => 'client']);
        $driverOwner = User::factory()->create(['role' => 'driver']);

        $client = Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $managerA->id,
            'user_id' => $clientOwner->id,
            'category_id' => $clientCategory->id,
            'company_name' => 'Owner Client',
        ]);

        $driver = Driver::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $managerA->id,
            'user_id' => $driverOwner->id,
            'license_number' => 'LIC-2001',
            'nid_number' => 'NID-2001',
            'driving_type' => 'backup',
            'joining_date' => now()->toDateString(),
        ]);

        $truck = Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $managerA->id,
            'truck_number' => 'TRK-2001',
            'model' => 'Model B',
            'status_id' => $truckStatus->id,
        ]);

        $trip = Trip::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_code' => 'T-2001',
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'created_by' => $managerA->id,
            'status_id' => $tripStatus->id,
            'pickup_point' => 'A',
            'delivery_point' => 'B',
            'load_date' => now(),
            'trip_rate' => 2000,
            'total_income' => 2000,
        ]);

        self::assertTrue(Gate::forUser($managerA)->allows('update', $client));
        self::assertTrue(Gate::forUser($managerA)->allows('delete', $client));
        self::assertTrue(Gate::forUser($managerA)->allows('toggleStatus', $client));

        self::assertTrue(Gate::forUser($managerA)->allows('update', $driver));
        self::assertTrue(Gate::forUser($managerA)->allows('delete', $driver));
        self::assertTrue(Gate::forUser($managerA)->allows('toggleStatus', $driver));
        self::assertTrue(Gate::forUser($managerA)->allows('toggleApproval', $driver));

        self::assertTrue(Gate::forUser($managerA)->allows('update', $truck));
        self::assertTrue(Gate::forUser($managerA)->allows('delete', $truck));
        self::assertTrue(Gate::forUser($managerA)->allows('updateStatus', $truck));

        self::assertTrue(Gate::forUser($managerA)->allows('updateStatus', $trip));
        self::assertTrue(Gate::forUser($managerA)->allows('recordExpense', $trip));
        self::assertTrue(Gate::forUser($managerA)->allows('recordPayment', $trip));
        self::assertTrue(Gate::forUser($managerA)->allows('generateInvoice', $trip));
    }
}

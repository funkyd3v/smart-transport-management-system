<?php

declare(strict_types=1);

namespace Tests\Feature\Driver\Concerns;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Support\Str;

trait CreatesDriverFixtures
{
    private function createManager(): User
    {
        return User::factory()->create([
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);
    }

    private function createDriverUser(bool $verified = true): User
    {
        return User::factory()->create([
            'role' => 'driver',
            'email_verified_at' => $verified ? now() : null,
        ]);
    }

    private function createUserWithRole(string $role, bool $verified = true): User
    {
        return User::factory()->create([
            'role' => $role,
            'email_verified_at' => $verified ? now() : null,
        ]);
    }

    private function makeDriverProfile(User $manager, User $driverUser): Driver
    {
        return Driver::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'user_id' => $driverUser->id,
            'license_number' => 'LIC-'.Str::upper(Str::random(8)),
            'nid_number' => 'NID-'.Str::upper(Str::random(8)),
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ]);
    }

    private function makeClient(User $manager): Client
    {
        $clientUser = $this->createUserWithRole('client');
        $category = ClientCategory::query()->firstOrCreate(['name' => 'General']);

        return Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'user_id' => $clientUser->id,
            'category_id' => $category->id,
            'company_name' => 'Client Co '.Str::upper(Str::random(3)),
        ]);
    }

    private function makeIdleTruck(User $manager): Truck
    {
        $truckStatus = TruckStatus::query()->firstOrCreate(['name' => 'idle']);

        return Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'truck_number' => 'TRK-'.Str::upper(Str::random(6)),
            'model' => 'Model X',
            'status_id' => $truckStatus->id,
        ]);
    }

    private function makeTripForDriver(User $manager, Driver $driver, string $status = 'created'): Trip
    {
        $client = $this->makeClient($manager);
        $truck = $this->makeIdleTruck($manager);
        $tripStatus = TripStatus::query()->firstOrCreate(['name' => $status]);

        return Trip::query()->create([
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
            'trip_rate' => 10000,
            'advance_payment' => 1000,
            'total_income' => 10000,
            'total_expense' => 0,
            'due_amount' => 9000,
            'profit' => 10000,
        ]);
    }
}

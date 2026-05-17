<?php

declare(strict_types=1);

namespace Tests\Feature\Manager\Concerns;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait CreatesManagerFixtures
{
    private function createManager(): User
    {
        return User::factory()->create([
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);
    }

    private function makeClient(User $manager, ?User $clientUser = null): Client
    {
        $clientUser ??= User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $category = ClientCategory::query()->firstOrCreate(['name' => 'Port']);

        return Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'user_id' => $clientUser->id,
            'category_id' => $category->id,
            'company_name' => 'Test Company '.Str::random(4),
        ]);
    }

    private function makeDriver(User $manager, bool $approved = true): Driver
    {
        $driverUser = User::factory()->create([
            'role' => 'driver',
            'email_verified_at' => now(),
        ]);

        if ($approved) {
            DB::table('users')
                ->where('id', $driverUser->id)
                ->update(['approved_at' => now()->toDateTimeString()]);
        }

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

    private function makeIdleTruck(User $manager): Truck
    {
        $idleStatus = TruckStatus::query()->firstOrCreate(['name' => 'idle']);

        return Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'truck_number' => 'TRK-'.Str::upper(Str::random(6)),
            'model' => 'Test Model',
            'status_id' => $idleStatus->id,
        ]);
    }

    private function makeTrip(User $manager, string $statusName = 'created'): Trip
    {
        $client = $this->makeClient($manager);
        $driver = $this->makeDriver($manager);
        $truck = $this->makeIdleTruck($manager);
        $status = TripStatus::query()->firstOrCreate(['name' => $statusName]);

        return Trip::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_code' => 'TRIP-'.Str::upper(Str::random(6)),
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'created_by' => $manager->id,
            'status_id' => $status->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now(),
            'trip_rate' => 5000.00,
            'advance_payment' => 0,
            'total_income' => 5000.00,
            'due_amount' => 5000.00,
        ]);
    }
}

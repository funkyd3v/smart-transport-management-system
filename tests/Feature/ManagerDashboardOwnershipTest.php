<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Driver\Models\Driver;
use App\Modules\Due\Models\DueRecord;
use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ManagerDashboardOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_shows_all_due_data_but_collect_only_for_owned_due(): void
    {
        $managerA = User::factory()->create([
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);
        $managerB = User::factory()->create([
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);

        $tripOwnedByA = $this->createTripForManager($managerA, 'A Client Co', 'TRK-A', 'T-A');
        $tripOwnedByB = $this->createTripForManager($managerB, 'B Client Co', 'TRK-B', 'T-B');

        DueRecord::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_id' => $tripOwnedByA->id,
            'client_id' => $tripOwnedByA->client_id,
            'original_due' => 1000,
            'collected_amount' => 200,
            'remaining_due' => 800,
            'is_settled' => false,
        ]);

        DueRecord::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_id' => $tripOwnedByB->id,
            'client_id' => $tripOwnedByB->client_id,
            'original_due' => 1500,
            'collected_amount' => 0,
            'remaining_due' => 1500,
            'is_settled' => false,
        ]);

        $response = $this->actingAs($managerA)->get(route('manager.dashboard'));

        $response->assertOk();
        $response->assertSeeText('A Client Co');
        $response->assertSeeText('B Client Co');
        $response->assertSee(route('manager.trips.show', $tripOwnedByA), false);
        $response->assertDontSee(route('manager.trips.show', $tripOwnedByB), false);
        $response->assertSeeText('Not assigned');
    }

    public function test_non_owner_manager_cannot_see_expense_approval_actions_on_trip_details(): void
    {
        $ownerManager = User::factory()->create([
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);
        $otherManager = User::factory()->create([
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);

        $trip = $this->createTripForManager($ownerManager, 'Shared Client', 'TRK-S', 'T-S');
        $expenseCategory = ExpenseCategory::query()->create(['name' => 'Fuel']);

        $expense = TripExpense::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_id' => $trip->id,
            'category_id' => $expenseCategory->id,
            'recorded_by' => $ownerManager->id,
            'amount' => 500,
            'description' => 'Fuel refill',
            'expense_date' => now()->toDateString(),
            'is_approved' => false,
            'is_rejected' => false,
        ]);

        $response = $this->actingAs($otherManager)->get(route('manager.trips.show', $trip));

        $response->assertOk();
        $response->assertDontSee(route('manager.trips.expenses.approve', [$trip, $expense]), false);
        $response->assertDontSee(route('manager.trips.expenses.reject', [$trip, $expense]), false);
    }

    private function createTripForManager(User $manager, string $companyName, string $truckNumber, string $tripCode): Trip
    {
        $clientCategory = ClientCategory::query()->firstOrCreate(['name' => 'General']);
        $truckStatus = TruckStatus::query()->firstOrCreate(['name' => 'idle']);
        $tripStatus = TripStatus::query()->firstOrCreate(['name' => 'created']);

        $clientUser = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $driverUser = User::factory()->create([
            'role' => 'driver',
            'email_verified_at' => now(),
        ]);

        $client = Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'user_id' => $clientUser->id,
            'category_id' => $clientCategory->id,
            'company_name' => $companyName,
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

        $truck = Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'truck_number' => $truckNumber,
            'model' => 'Model X',
            'status_id' => $truckStatus->id,
        ]);

        return Trip::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_code' => $tripCode,
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'created_by' => $manager->id,
            'status_id' => $tripStatus->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now(),
            'trip_rate' => 2000,
            'total_income' => 2000,
            'due_amount' => 2000,
        ]);
    }
}

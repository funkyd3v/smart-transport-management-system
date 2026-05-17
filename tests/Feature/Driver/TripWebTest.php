<?php

declare(strict_types=1);

namespace Tests\Feature\Driver;

use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Driver\Concerns\CreatesDriverFixtures;
use Tests\TestCase;

class TripWebTest extends TestCase
{
    use CreatesDriverFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_from_driver_trip_endpoints(): void
    {
        $this->get('/driver/trips')->assertRedirect(route('login'));
        $this->post('/driver/trips/status', [])->assertRedirect(route('login'));
    }

    public function test_non_driver_cannot_access_driver_trip_endpoints(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, 'created');

        $this->actingAs($manager)
            ->get('/driver/trips')
            ->assertForbidden();

        $this->actingAs($manager)
            ->patch('/driver/trips/'.$trip->ulid.'/status', ['status' => TripStatusEnum::InProgress->value])
            ->assertForbidden();
    }

    public function test_driver_can_list_and_view_own_trip(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, 'created');

        $this->actingAs($driverUser)
            ->get('/driver/trips')
            ->assertOk()
            ->assertSee((string) $trip->trip_code);

        $this->actingAs($driverUser)
            ->get('/driver/trips/'.$trip->ulid)
            ->assertOk();
    }

    public function test_driver_cannot_view_another_drivers_trip(): void
    {
        $manager = $this->createManager();

        $ownerUser = $this->createDriverUser();
        $ownerDriver = $this->makeDriverProfile($manager, $ownerUser);
        $trip = $this->makeTripForDriver($manager, $ownerDriver, 'created');

        $otherDriverUser = $this->createDriverUser();
        $this->makeDriverProfile($manager, $otherDriverUser);

        $this->actingAs($otherDriverUser)
            ->get('/driver/trips/'.$trip->ulid)
            ->assertNotFound();
    }

    public function test_driver_can_request_trip_completion_via_driver_module_endpoint(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, TripStatusEnum::InProgress->value);

        $this->actingAs($driverUser)
            ->patchJson('/driver/trips/'.$trip->ulid.'/status', [
                'status' => TripStatusEnum::Completed->value,
            ])
            ->assertOk()
            ->assertJsonPath('trip.status', TripStatusEnum::InProgress->value);

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'completion_requested_by' => $driverUser->id,
        ]);
    }

    public function test_driver_module_status_update_validates_transition_rules(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, 'created');

        $this->actingAs($driverUser)
            ->patchJson('/driver/trips/'.$trip->ulid.'/status', [
                'status' => TripStatusEnum::Completed->value,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_driver_can_record_expense_on_in_progress_trip(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, TripStatusEnum::InProgress->value);

        ExpenseCategory::query()->firstOrCreate(['name' => 'Fuel']);

        $this->actingAs($driverUser)
            ->postJson('/driver/trips/'.$trip->ulid.'/expenses', [
                'category' => 'fuel',
                'amount' => 850,
                'description' => 'Fuel refill',
                'expense_date' => now()->toDateString(),
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Expense recorded successfully.');

        $this->assertDatabaseHas('trip_expenses', [
            'trip_id' => $trip->id,
            'amount' => 850,
            'is_approved' => false,
        ]);
    }

    public function test_driver_cannot_record_expense_on_other_drivers_trip(): void
    {
        $manager = $this->createManager();

        $ownerUser = $this->createDriverUser();
        $ownerDriver = $this->makeDriverProfile($manager, $ownerUser);
        $trip = $this->makeTripForDriver($manager, $ownerDriver, TripStatusEnum::InProgress->value);

        $otherDriverUser = $this->createDriverUser();
        $this->makeDriverProfile($manager, $otherDriverUser);

        $this->actingAs($otherDriverUser)
            ->postJson('/driver/trips/'.$trip->ulid.'/expenses', [
                'category' => 'fuel',
                'amount' => 850,
                'expense_date' => now()->toDateString(),
            ])
            ->assertNotFound();
    }

    public function test_driver_can_add_reload_via_driver_module_endpoint(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, TripStatusEnum::InProgress->value);

        $this->actingAs($driverUser)
            ->postJson('/driver/trips/'.$trip->ulid.'/reloads', [
                'location' => 'Comilla Station',
                'reload_amount' => 1500,
                'note' => 'Top-up fuel',
                'reloaded_at' => now()->toDateTimeString(),
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Reload history added successfully.');

        $this->assertDatabaseHas('reload_history', [
            'trip_id' => $trip->id,
            'reload_point' => 'Comilla Station',
        ]);
    }

    public function test_driver_can_add_reload_via_legacy_trip_endpoint(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, TripStatusEnum::InProgress->value);

        $this->actingAs($driverUser)
            ->post('/driver/trips/'.$trip->ulid.'/reload', [
                'reload_point' => 'Sylhet Depot',
                'note' => 'Refuel',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reload_history', [
            'trip_id' => $trip->id,
            'reload_point' => 'Sylhet Depot',
        ]);
    }

    public function test_legacy_status_update_endpoint_validates_required_trip_ulid(): void
    {
        $driverUser = $this->createDriverUser();

        $this->actingAs($driverUser)
            ->post('/driver/trips/status', [
                'status' => TripStatusEnum::InProgress->value,
            ])
            ->assertSessionHasErrors(['trip_ulid']);
    }
}

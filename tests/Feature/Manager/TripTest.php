<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class TripTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_trip_list(): void
    {
        $this->get(route('manager.trips.index'))
            ->assertRedirect(route('login'));
    }

    public function test_manager_can_list_trips(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.trips.index'))
            ->assertOk();
    }

    public function test_manager_can_view_the_create_trip_page(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.trips.create'))
            ->assertOk();
    }

    public function test_manager_can_create_a_trip(): void
    {
        $manager = $this->createManager();
        $client = $this->makeClient($manager);
        $driver = $this->makeDriver($manager);
        $truck = $this->makeIdleTruck($manager);

        $payload = [
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now()->toDateString(),
            'trip_rate' => 5000,
            'goods' => [
                [
                    'item_name' => 'Electronics',
                    'unit' => 'carton',
                    'quantity' => 10,
                    'unit_price' => 500,
                ],
            ],
        ];

        $response = $this->actingAs($manager)->post(route('manager.trips.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('trips', ['pickup_point' => 'Dhaka', 'created_by' => $manager->id]);
    }

    public function test_trip_store_rejects_inactive_client(): void
    {
        $manager = $this->createManager();
        $clientUser = User::factory()->create([
            'role' => 'client',
            'is_active' => false,
        ]);
        $category = ClientCategory::query()->firstOrCreate(['name' => 'Port']);
        $client = Client::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'user_id' => $clientUser->id,
            'category_id' => $category->id,
            'company_name' => 'Inactive Client Co',
        ]);
        $driver = $this->makeDriver($manager);
        $truck = $this->makeIdleTruck($manager);

        $payload = [
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now()->toDateString(),
            'trip_rate' => 5000,
            'goods' => [
                ['item_name' => 'Goods', 'unit' => 'box', 'quantity' => 1, 'unit_price' => 100],
            ],
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trips.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['client_id']);
    }

    public function test_trip_store_rejects_unapproved_driver(): void
    {
        $manager = $this->createManager();
        $client = $this->makeClient($manager);
        $driver = $this->makeDriver($manager, approved: false);
        $truck = $this->makeIdleTruck($manager);

        $payload = [
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now()->toDateString(),
            'trip_rate' => 5000,
            'goods' => [
                ['item_name' => 'Goods', 'unit' => 'box', 'quantity' => 1, 'unit_price' => 100],
            ],
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trips.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['driver_id']);
    }

    public function test_trip_store_rejects_non_idle_truck(): void
    {
        $manager = $this->createManager();
        $client = $this->makeClient($manager);
        $driver = $this->makeDriver($manager);
        $workshopStatus = TruckStatus::query()->firstOrCreate(['name' => 'under_workshop']);
        $truck = Truck::query()->create([
            'ulid' => (string) Str::ulid(),
            'created_by' => $manager->id,
            'truck_number' => 'TRK-WS-001',
            'model' => 'Workshop Truck',
            'status_id' => $workshopStatus->id,
        ]);

        $payload = [
            'client_id' => $client->id,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'pickup_point' => 'Dhaka',
            'delivery_point' => 'Chattogram',
            'load_date' => now()->toDateString(),
            'trip_rate' => 5000,
            'goods' => [
                ['item_name' => 'Goods', 'unit' => 'box', 'quantity' => 1, 'unit_price' => 100],
            ],
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trips.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['truck_id']);
    }

    public function test_manager_can_view_any_trip(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $trip = $this->makeTrip($otherManager);

        $this->actingAs($manager)
            ->get(route('manager.trips.show', $trip))
            ->assertOk();
    }

    public function test_manager_can_update_status_of_own_trip(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager, 'created');

        TripStatus::query()->firstOrCreate(['name' => 'in_progress']);

        $this->actingAs($manager)
            ->patchJson(route('manager.trips.update-status', $trip), ['status' => 'in_progress'])
            ->assertOk()
            ->assertJson(['message' => 'Trip status updated successfully.']);
    }

    public function test_manager_cannot_update_status_of_another_managers_trip(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $trip = $this->makeTrip($otherManager, 'created');

        $this->actingAs($manager)
            ->patchJson(route('manager.trips.update-status', $trip), ['status' => 'in_progress'])
            ->assertForbidden();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class TruckTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_truck_list(): void
    {
        $this->get(route('manager.trucks.index'))
            ->assertRedirect(route('login'));
    }

    public function test_manager_can_list_trucks(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.trucks.index'))
            ->assertOk();
    }

    public function test_manager_can_view_the_create_truck_page(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.trucks.create'))
            ->assertOk();
    }

    public function test_manager_can_create_a_truck(): void
    {
        $manager = $this->createManager();

        $payload = [
            'truck_number' => 'METRO-T-001',
            'truck_type' => 'Heavy',
            'capacity' => 10,
            'status' => 'idle',
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trucks.store'), $payload)
            ->assertOk()
            ->assertJson(['message' => 'Truck created successfully.']);

        $this->assertDatabaseHas('trucks', ['truck_number' => 'METRO-T-001']);
    }

    public function test_truck_store_rejects_duplicate_truck_number(): void
    {
        $manager = $this->createManager();
        $existingTruck = $this->makeIdleTruck($manager);

        $payload = [
            'truck_number' => $existingTruck->truck_number,
            'truck_type' => 'Light',
            'capacity' => 5,
            'status' => 'idle',
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trucks.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['truck_number']);
    }

    public function test_manager_can_view_any_truck(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $truck = $this->makeIdleTruck($otherManager);

        $this->actingAs($manager)
            ->get(route('manager.trucks.show', $truck))
            ->assertOk();
    }

    public function test_manager_can_update_own_truck(): void
    {
        $manager = $this->createManager();
        $truck = $this->makeIdleTruck($manager);

        $payload = [
            'truck_number' => $truck->truck_number,
            'truck_type' => 'Medium',
            'capacity' => 8,
            'status' => 'idle',
        ];

        $this->actingAs($manager)
            ->putJson(route('manager.trucks.update', $truck), $payload)
            ->assertOk()
            ->assertJson(['message' => 'Truck updated successfully.']);
    }

    public function test_manager_cannot_update_another_managers_truck(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $truck = $this->makeIdleTruck($otherManager);

        $payload = [
            'truck_number' => $truck->truck_number,
            'truck_type' => 'Medium',
            'capacity' => 8,
            'status' => 'idle',
        ];

        $this->actingAs($manager)
            ->putJson(route('manager.trucks.update', $truck), $payload)
            ->assertForbidden();
    }

    public function test_manager_can_delete_own_truck(): void
    {
        $manager = $this->createManager();
        $truck = $this->makeIdleTruck($manager);

        $this->actingAs($manager)
            ->deleteJson(route('manager.trucks.destroy', $truck))
            ->assertOk()
            ->assertJson(['message' => 'Truck deleted successfully.']);

        $this->assertSoftDeleted('trucks', ['id' => $truck->id]);
    }

    public function test_manager_cannot_delete_another_managers_truck(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $truck = $this->makeIdleTruck($otherManager);

        $this->actingAs($manager)
            ->deleteJson(route('manager.trucks.destroy', $truck))
            ->assertForbidden();
    }

    public function test_manager_can_update_status_of_own_truck(): void
    {
        $manager = $this->createManager();
        $truck = $this->makeIdleTruck($manager);

        TruckStatus::query()->firstOrCreate(['name' => 'under_workshop']);

        $this->actingAs($manager)
            ->patchJson(route('manager.trucks.update-status', $truck), ['status' => 'under_workshop'])
            ->assertOk()
            ->assertJson(['message' => 'Truck status updated successfully.', 'status' => 'under_workshop']);
    }

    public function test_manager_cannot_update_status_of_another_managers_truck(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $truck = $this->makeIdleTruck($otherManager);

        $this->actingAs($manager)
            ->patchJson(route('manager.trucks.update-status', $truck), ['status' => 'under_workshop'])
            ->assertForbidden();
    }
}

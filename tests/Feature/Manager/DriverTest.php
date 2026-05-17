<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_driver_list(): void
    {
        $this->get(route('manager.drivers.index'))
            ->assertRedirect(route('login'));
    }

    public function test_manager_can_list_drivers(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.drivers.index'))
            ->assertOk();
    }

    public function test_manager_can_view_the_create_driver_page(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.drivers.create'))
            ->assertOk();
    }

    public function test_manager_can_create_a_driver(): void
    {
        $manager = $this->createManager();

        $payload = [
            'name' => 'John Driver',
            'email' => 'john.driver@example.com',
            'password' => 'password123',
            'mobile_number' => '01711111111',
            'license_number' => 'LIC-TEST-001',
            'nid_number' => 'NID-TEST-001',
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.drivers.store'), $payload)
            ->assertCreated()
            ->assertJsonStructure(['message', 'driver', 'credentials']);

        $this->assertDatabaseHas('drivers', ['license_number' => 'LIC-TEST-001']);
    }

    public function test_driver_store_rejects_duplicate_email(): void
    {
        $manager = $this->createManager();
        $existingUser = User::factory()->create(['email' => 'dup@example.com', 'role' => 'driver']);

        $payload = [
            'name' => 'Another Driver',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'mobile_number' => '01711111112',
            'license_number' => 'LIC-TEST-002',
            'nid_number' => 'NID-TEST-002',
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.drivers.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_driver_store_rejects_duplicate_license_number(): void
    {
        $manager = $this->createManager();
        $existingDriver = $this->makeDriver($manager);

        $payload = [
            'name' => 'Copy Driver',
            'email' => 'copy.driver@example.com',
            'password' => 'password123',
            'mobile_number' => '01711111113',
            'license_number' => $existingDriver->license_number,
            'nid_number' => 'NID-TEST-UNIQUE-001',
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.drivers.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['license_number']);
    }

    public function test_manager_can_view_any_driver(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $driver = $this->makeDriver($otherManager);

        $this->actingAs($manager)
            ->get(route('manager.drivers.show', $driver))
            ->assertOk();
    }

    public function test_manager_can_update_own_driver(): void
    {
        $manager = $this->createManager();
        $driver = $this->makeDriver($manager);

        $payload = [
            'name' => 'Updated Driver Name',
            'mobile_number' => '01711111114',
            'license_number' => $driver->license_number,
            'nid_number' => $driver->nid_number,
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->putJson(route('manager.drivers.update', $driver), $payload)
            ->assertOk()
            ->assertJson(['message' => 'Driver updated successfully.']);
    }

    public function test_manager_cannot_update_another_managers_driver(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $driver = $this->makeDriver($otherManager);

        $payload = [
            'name' => 'Hijacked Name',
            'mobile_number' => '01711111115',
            'license_number' => $driver->license_number,
            'nid_number' => $driver->nid_number,
            'driving_type' => 'permanent',
            'joining_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->putJson(route('manager.drivers.update', $driver), $payload)
            ->assertForbidden();
    }

    public function test_manager_can_delete_own_driver(): void
    {
        $manager = $this->createManager();
        $driver = $this->makeDriver($manager);

        $this->actingAs($manager)
            ->deleteJson(route('manager.drivers.destroy', $driver))
            ->assertOk()
            ->assertJson(['message' => 'Driver deleted successfully.']);

        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }

    public function test_manager_cannot_delete_another_managers_driver(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $driver = $this->makeDriver($otherManager);

        $this->actingAs($manager)
            ->deleteJson(route('manager.drivers.destroy', $driver))
            ->assertForbidden();
    }

    public function test_manager_can_toggle_status_of_own_driver(): void
    {
        $manager = $this->createManager();
        $driver = $this->makeDriver($manager);

        $this->actingAs($manager)
            ->patchJson(route('manager.drivers.toggle-status', $driver))
            ->assertOk()
            ->assertJsonStructure(['message', 'status']);
    }

    public function test_manager_cannot_toggle_status_of_another_managers_driver(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $driver = $this->makeDriver($otherManager);

        $this->actingAs($manager)
            ->patchJson(route('manager.drivers.toggle-status', $driver))
            ->assertForbidden();
    }

    public function test_manager_can_toggle_approval_of_own_driver(): void
    {
        $manager = $this->createManager();
        $driver = $this->makeDriver($manager);

        $this->actingAs($manager)
            ->patchJson(route('manager.drivers.toggle-approval', $driver))
            ->assertOk()
            ->assertJsonStructure(['message', 'is_approved']);
    }

    public function test_manager_cannot_toggle_approval_of_another_managers_driver(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $driver = $this->makeDriver($otherManager);

        $this->actingAs($manager)
            ->patchJson(route('manager.drivers.toggle-approval', $driver))
            ->assertForbidden();
    }
}

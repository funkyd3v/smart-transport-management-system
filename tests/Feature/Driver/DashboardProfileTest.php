<?php

declare(strict_types=1);

namespace Tests\Feature\Driver;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Driver\Concerns\CreatesDriverFixtures;
use Tests\TestCase;

class DashboardProfileTest extends TestCase
{
    use CreatesDriverFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_from_driver_dashboard_and_profile(): void
    {
        $this->get('/driver/dashboard')->assertRedirect(route('login'));
        $this->get('/driver/profile')->assertRedirect(route('login'));
    }

    public function test_non_driver_cannot_access_driver_dashboard_or_profile(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get('/driver/dashboard')
            ->assertForbidden();

        $this->actingAs($manager)
            ->get('/driver/profile')
            ->assertForbidden();
    }

    public function test_driver_can_view_dashboard_without_driver_profile_row(): void
    {
        $driverUser = $this->createDriverUser();

        $this->actingAs($driverUser)
            ->get('/driver/dashboard')
            ->assertOk();
    }

    public function test_driver_can_view_dashboard_with_assigned_trips(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $trip = $this->makeTripForDriver($manager, $driver, 'created');

        $this->actingAs($driverUser)
            ->get('/driver/dashboard')
            ->assertOk()
            ->assertSee((string) $trip->trip_code);
    }

    public function test_driver_can_view_profile_page(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $this->makeDriverProfile($manager, $driverUser);

        $this->actingAs($driverUser)
            ->get('/driver/profile')
            ->assertOk();
    }

    public function test_driver_can_update_profile_name_and_phone(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $driver = $this->makeDriverProfile($manager, $driverUser);

        $this->actingAs($driverUser)
            ->patch('/driver/profile', [
                'name' => 'Updated Driver Name',
                'phone' => '01711111119',
            ])
            ->assertRedirect(route('driver.profile'))
            ->assertSessionHas('success', 'Profile updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $driverUser->id,
            'name' => 'Updated Driver Name',
            'phone' => '01711111119',
        ]);
    }

    public function test_driver_profile_update_validates_required_name(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser();
        $this->makeDriverProfile($manager, $driverUser);

        $this->actingAs($driverUser)
            ->patch('/driver/profile', [
                'name' => '',
                'phone' => '01711111119',
            ])
            ->assertSessionHasErrors(['name']);
    }

    public function test_unverified_driver_is_redirected_from_verified_routes(): void
    {
        $driverUser = $this->createDriverUser(false);

        $this->actingAs($driverUser)
            ->get('/driver/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_legacy_trip_index_is_available_for_driver_role(): void
    {
        $manager = $this->createManager();
        $driverUser = $this->createDriverUser(false);
        $driver = $this->makeDriverProfile($manager, $driverUser);
        $this->makeTripForDriver($manager, $driver);

        $this->actingAs($driverUser)
            ->get('/driver/trips')
            ->assertOk();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('manager.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_non_manager_role_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('manager.dashboard'))
            ->assertForbidden();
    }

    public function test_driver_role_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'driver', 'email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('manager.dashboard'))
            ->assertForbidden();
    }

    public function test_manager_can_access_dashboard(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_from_manager_profile_page(): void
    {
        $this->get(route('manager.profile'))
            ->assertRedirect(route('login'));
    }

    public function test_manager_can_view_profile_page(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.profile'))
            ->assertOk()
            ->assertSee('Manager Profile');
    }

    public function test_manager_can_update_profile_via_ajax(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->patchJson(route('manager.profile.update'), [
                'name' => 'Updated Manager',
                'email' => 'updated.manager@example.com',
                'phone' => '01712345678',
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'Profile updated successfully.',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $manager->id,
            'name' => 'Updated Manager',
            'email' => 'updated.manager@example.com',
            'phone' => '01712345678',
        ]);
    }

    public function test_manager_profile_update_rejects_duplicate_email_or_phone(): void
    {
        $manager = $this->createManager();
        $existingUser = User::factory()->create([
            'email' => 'taken@example.com',
            'phone' => '01811111111',
        ]);

        $this->actingAs($manager)
            ->patchJson(route('manager.profile.update'), [
                'name' => 'Manager Name',
                'email' => $existingUser->email,
                'phone' => $existingUser->phone,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'phone']);
    }

    public function test_manager_can_change_password_via_ajax(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->putJson(route('password.update'), [
                'current_password' => 'password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'Password updated successfully.',
            ]);

        $this->assertTrue(Hash::check('new-secure-password', $manager->refresh()->password));
    }

    public function test_manager_password_change_requires_correct_current_password(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->putJson(route('password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }
}

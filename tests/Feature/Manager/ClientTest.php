<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_client_list(): void
    {
        $this->get(route('manager.clients.index'))
            ->assertRedirect(route('login'));
    }

    public function test_manager_can_list_clients(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.clients.index'))
            ->assertOk();
    }

    public function test_manager_can_view_the_create_client_page(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->get(route('manager.clients.create'))
            ->assertOk();
    }

    public function test_manager_can_create_a_port_type_client(): void
    {
        $manager = $this->createManager();

        $payload = [
            'name' => 'Acme Shipping',
            'contact_number' => '01711111111',
            'client_type' => 'port',
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.clients.store'), $payload)
            ->assertOk()
            ->assertJson(['message' => 'Client created successfully.']);

        $this->assertDatabaseHas('clients', ['company_name' => 'Acme Shipping']);
    }

    public function test_client_store_requires_name_and_contact_number(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)
            ->postJson(route('manager.clients.store'), ['client_type' => 'port'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'contact_number']);
    }

    public function test_client_store_requires_project_fields_for_contractual_type(): void
    {
        $manager = $this->createManager();

        $payload = [
            'name' => 'Contractual Co',
            'contact_number' => '01711111112',
            'client_type' => 'contractual',
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.clients.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['project', 'project_agreement_number', 'project_value']);
    }

    public function test_manager_can_view_any_client(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $client = $this->makeClient($otherManager);

        $this->actingAs($manager)
            ->get(route('manager.clients.show', $client))
            ->assertOk();
    }

    public function test_manager_can_update_own_client(): void
    {
        $manager = $this->createManager();
        $client = $this->makeClient($manager);

        $payload = [
            'name' => 'Updated Company Name',
            'contact_number' => '01811111111',
            'client_type' => 'port',
        ];

        $this->actingAs($manager)
            ->putJson(route('manager.clients.update', $client), $payload)
            ->assertOk()
            ->assertJson(['message' => 'Client updated successfully.']);
    }

    public function test_manager_cannot_update_another_managers_client(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $client = $this->makeClient($otherManager);

        $payload = [
            'name' => 'Hijacked Name',
            'contact_number' => '01811111111',
            'client_type' => 'port',
        ];

        $this->actingAs($manager)
            ->putJson(route('manager.clients.update', $client), $payload)
            ->assertForbidden();
    }

    public function test_manager_can_delete_own_client(): void
    {
        $manager = $this->createManager();
        $client = $this->makeClient($manager);

        $this->actingAs($manager)
            ->deleteJson(route('manager.clients.destroy', $client))
            ->assertOk()
            ->assertJson(['message' => 'Client deleted successfully.']);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_manager_cannot_delete_another_managers_client(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $client = $this->makeClient($otherManager);

        $this->actingAs($manager)
            ->deleteJson(route('manager.clients.destroy', $client))
            ->assertForbidden();
    }

    public function test_manager_can_toggle_status_of_own_client(): void
    {
        $manager = $this->createManager();
        $client = $this->makeClient($manager);

        $this->actingAs($manager)
            ->patchJson(route('manager.clients.toggle-status', $client))
            ->assertOk()
            ->assertJsonStructure(['message', 'status']);
    }

    public function test_manager_cannot_toggle_status_of_another_managers_client(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $client = $this->makeClient($otherManager);

        $this->actingAs($manager)
            ->patchJson(route('manager.clients.toggle-status', $client))
            ->assertForbidden();
    }
}

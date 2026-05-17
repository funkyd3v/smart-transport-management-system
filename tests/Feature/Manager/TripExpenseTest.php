<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Trip\Models\TripExpense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class TripExpenseTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    private function makeExpense(int $tripId, int $recordedBy, bool $approved = false, bool $rejected = false): TripExpense
    {
        $category = ExpenseCategory::query()->firstOrCreate(['name' => 'Fuel']);

        return TripExpense::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_id' => $tripId,
            'category_id' => $category->id,
            'recorded_by' => $recordedBy,
            'amount' => 500.00,
            'description' => 'Test expense',
            'expense_date' => now()->toDateString(),
            'is_approved' => $approved,
            'is_rejected' => $rejected,
        ]);
    }

    public function test_manager_can_record_expense_on_own_trip(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager, 'in_progress');
        $category = ExpenseCategory::query()->firstOrCreate(['name' => 'Fuel']);

        $payload = [
            'trip_ulid' => $trip->ulid,
            'category_id' => $category->id,
            'amount' => 750.00,
            'description' => 'Fuel refill',
            'expense_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.store', $trip), $payload)
            ->assertOk()
            ->assertJsonStructure(['message', 'expense']);

        $this->assertDatabaseHas('trip_expenses', ['trip_id' => $trip->id, 'amount' => 750.00]);
    }

    public function test_manager_cannot_record_expense_on_another_managers_trip(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $trip = $this->makeTrip($otherManager);
        $category = ExpenseCategory::query()->firstOrCreate(['name' => 'Fuel']);

        $payload = [
            'trip_ulid' => $trip->ulid,
            'category_id' => $category->id,
            'amount' => 300.00,
            'expense_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.store', $trip), $payload)
            ->assertForbidden();
    }

    public function test_manager_can_approve_a_pending_expense(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $expense = $this->makeExpense($trip->id, $manager->id);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.approve', [$trip, $expense]))
            ->assertOk()
            ->assertJson(['message' => 'Expense approved successfully.'])
            ->assertJsonPath('expense.is_approved', true);

        $this->assertDatabaseHas('trip_expenses', ['id' => $expense->id, 'is_approved' => true]);
    }

    public function test_cannot_approve_an_already_approved_expense(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $expense = $this->makeExpense($trip->id, $manager->id, approved: true);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.approve', [$trip, $expense]))
            ->assertStatus(422);
    }

    public function test_cannot_approve_a_rejected_expense(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $expense = $this->makeExpense($trip->id, $manager->id, rejected: true);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.approve', [$trip, $expense]))
            ->assertStatus(422);
    }

    public function test_manager_can_reject_a_pending_expense(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $expense = $this->makeExpense($trip->id, $manager->id);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.reject', [$trip, $expense]))
            ->assertOk()
            ->assertJson(['message' => 'Expense rejected successfully.'])
            ->assertJsonPath('expense.is_rejected', true);

        $this->assertDatabaseHas('trip_expenses', ['id' => $expense->id, 'is_rejected' => true]);
    }

    public function test_cannot_reject_an_already_rejected_expense(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $expense = $this->makeExpense($trip->id, $manager->id, rejected: true);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.reject', [$trip, $expense]))
            ->assertStatus(422);
    }

    public function test_cannot_reject_an_approved_expense(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $expense = $this->makeExpense($trip->id, $manager->id, approved: true);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.reject', [$trip, $expense]))
            ->assertStatus(422);
    }

    public function test_manager_cannot_approve_expense_on_another_managers_trip(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $trip = $this->makeTrip($otherManager);
        $expense = $this->makeExpense($trip->id, $otherManager->id);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.expenses.approve', [$trip, $expense]))
            ->assertForbidden();
    }
}

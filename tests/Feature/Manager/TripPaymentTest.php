<?php

declare(strict_types=1);

namespace Tests\Feature\Manager;

use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Trip\Models\DueRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Manager\Concerns\CreatesManagerFixtures;
use Tests\TestCase;

class TripPaymentTest extends TestCase
{
    use CreatesManagerFixtures;
    use RefreshDatabase;

    private function paymentPayload(int $methodId, string $tripUlid, int $clientId): array
    {
        return [
            'trip_ulid' => $tripUlid,
            'client_id' => $clientId,
            'payment_method_id' => $methodId,
            'amount' => 2000.00,
            'payment_date' => now()->toDateString(),
            'transaction_reference' => 'TXN-TEST-001',
        ];
    }

    public function test_manager_can_record_payment_on_own_trip(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);
        $method = PaymentMethod::query()->create(['name' => 'Bank Transfer']);

        DueRecord::query()->create([
            'ulid' => (string) Str::ulid(),
            'trip_id' => $trip->id,
            'client_id' => $trip->client_id,
            'original_due' => 5000,
            'collected_amount' => 0,
            'remaining_due' => 5000,
            'is_settled' => false,
        ]);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.payments.store', $trip), $this->paymentPayload($method->id, $trip->ulid, $trip->client_id))
            ->assertOk()
            ->assertJsonStructure(['message', 'payment']);

        $this->assertDatabaseHas('payments', ['trip_id' => $trip->id, 'amount' => 2000.00]);
    }

    public function test_manager_cannot_record_payment_on_another_managers_trip(): void
    {
        $manager = $this->createManager();
        $otherManager = $this->createManager();
        $trip = $this->makeTrip($otherManager);
        $method = PaymentMethod::query()->create(['name' => 'Cash']);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.payments.store', $trip), $this->paymentPayload($method->id, $trip->ulid, $trip->client_id))
            ->assertForbidden();
    }

    public function test_payment_store_validates_required_fields(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);

        $this->actingAs($manager)
            ->postJson(route('manager.trips.payments.store', $trip), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_method_id', 'amount', 'payment_date']);
    }

    public function test_payment_store_rejects_non_existent_payment_method(): void
    {
        $manager = $this->createManager();
        $trip = $this->makeTrip($manager);

        $payload = [
            'trip_ulid' => $trip->ulid,
            'client_id' => $trip->client_id,
            'payment_method_id' => 99999,
            'amount' => 1000.00,
            'payment_date' => now()->toDateString(),
        ];

        $this->actingAs($manager)
            ->postJson(route('manager.trips.payments.store', $trip), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_method_id']);
    }
}

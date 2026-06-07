<?php

declare(strict_types=1);

namespace Tests\Feature\Communication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommunicationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_queue_sms_communication(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('api.communications.send'), [
            'channel' => 'sms',
            'recipient' => '+8801712345678',
            'subject' => 'Trip Update',
            'body' => 'Your trip has started.',
            'template_data' => [
                'trip_number' => 'TRIP-001',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Communication queued successfully.');

        $this->assertDatabaseHas('communications', [
            'channel' => 'sms',
            'recipient' => '+8801712345678',
            'status' => 'queued',
        ]);
    }

    public function test_otp_generate_endpoint_is_rate_limited(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('api.communications.otp.generate'), [
                'purpose' => 'login',
                'recipient' => '+8801712345678',
            ])->assertOk();
        }

        $this->postJson(route('api.communications.otp.generate'), [
            'purpose' => 'login',
            'recipient' => '+8801712345678',
        ])->assertStatus(429);
    }
}

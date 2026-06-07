<?php

declare(strict_types=1);

namespace Tests\Feature\Communication;

use App\Modules\Communication\Enums\CommunicationStatus;
use App\Modules\Communication\Models\Communication;
use App\Modules\Communication\Services\CommunicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BulkSmsFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_now_uses_bulksmsbd_default_provider_and_records_attempt(): void
    {
        config()->set('communication.default_providers.sms', 'bulksmsbd');
        config()->set('communication.providers.sms.bulksmsbd.api_key', 'api-key');
        config()->set('communication.providers.sms.bulksmsbd.sender_id', 'SENDER');
        config()->set('communication.providers.sms.bulksmsbd.endpoint', 'http://bulksmsbd.net/api/smsapi');

        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'smsid' => 'bulk-msg-001',
            ], 200),
        ]);

        $communication = Communication::query()->create([
            'ulid' => (string) str()->ulid()->toBase32(),
            'reference_no' => 'REFBULK001',
            'channel' => 'sms',
            'provider' => null,
            'recipient' => '+8801712345678',
            'subject' => null,
            'body' => 'Your OTP is 123456',
            'status' => CommunicationStatus::Sending->value,
        ]);

        $result = app(CommunicationService::class)->sendNow($communication);

        $this->assertTrue($result->success);

        $this->assertDatabaseHas('communications', [
            'id' => $communication->id,
            'status' => CommunicationStatus::Sent->value,
            'provider' => 'bulksmsbd',
            'provider_message_id' => 'bulk-msg-001',
        ]);

        $this->assertDatabaseHas('communication_attempts', [
            'communication_id' => $communication->id,
            'provider' => 'bulksmsbd',
            'status' => 'sent',
        ]);
    }

    public function test_send_now_marks_failed_on_invalid_provider_response(): void
    {
        config()->set('communication.default_providers.sms', 'bulksmsbd');
        config()->set('communication.providers.sms.bulksmsbd.api_key', 'api-key');
        config()->set('communication.providers.sms.bulksmsbd.sender_id', 'SENDER');
        config()->set('communication.providers.sms.bulksmsbd.endpoint', 'http://bulksmsbd.net/api/smsapi');

        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 1003,
                'error_message' => 'Please Required all fields',
            ], 200),
        ]);

        $communication = Communication::query()->create([
            'ulid' => (string) str()->ulid()->toBase32(),
            'reference_no' => 'REFBULK002',
            'channel' => 'sms',
            'provider' => 'bulksmsbd',
            'recipient' => '8801712345678',
            'subject' => null,
            'body' => 'Invalid request test',
            'status' => CommunicationStatus::Sending->value,
        ]);

        $result = app(CommunicationService::class)->sendNow($communication);

        $this->assertFalse($result->success);

        $this->assertDatabaseHas('communications', [
            'id' => $communication->id,
            'status' => CommunicationStatus::Failed->value,
            'provider' => 'bulksmsbd',
        ]);
    }
}

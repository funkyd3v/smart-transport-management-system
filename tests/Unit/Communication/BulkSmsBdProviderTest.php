<?php

declare(strict_types=1);

namespace Tests\Unit\Communication;

use App\Modules\Communication\Channels\SMS\Providers\BulkSmsBdProvider;
use App\Modules\Communication\Models\Communication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BulkSmsBdProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_sms_successfully_and_maps_response(): void
    {
        config()->set('communication.providers.sms.bulksmsbd.api_key', 'api-key');
        config()->set('communication.providers.sms.bulksmsbd.sender_id', 'SENDER');
        config()->set('communication.providers.sms.bulksmsbd.endpoint', 'http://bulksmsbd.net/api/smsapi');

        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
                'smsid' => 'abc123',
            ], 200),
        ]);

        $provider = new BulkSmsBdProvider();
        $result = $provider->send($this->makeCommunication('+8801712345678'));

        $this->assertTrue($result->success);
        $this->assertSame('bulksmsbd', $result->provider);
        $this->assertSame('202', $result->responseCode);
        $this->assertSame('abc123', $result->providerMessageId);
        $this->assertSame('sent', $result->status);
    }

    public function test_it_fails_safely_when_credentials_missing(): void
    {
        config()->set('communication.providers.sms.bulksmsbd.api_key', '');
        config()->set('communication.providers.sms.bulksmsbd.sender_id', '');

        $provider = new BulkSmsBdProvider();
        $result = $provider->send($this->makeCommunication('8801712345678'));

        $this->assertFalse($result->success);
        $this->assertSame('bulksmsbd', $result->provider);
        $this->assertSame('config_missing', $result->responseCode);
        $this->assertSame('failed', $result->status);
    }

    public function test_it_handles_timeout_or_connection_errors_gracefully(): void
    {
        config()->set('communication.providers.sms.bulksmsbd.api_key', 'api-key');
        config()->set('communication.providers.sms.bulksmsbd.sender_id', 'SENDER');
        config()->set('communication.providers.sms.bulksmsbd.endpoint', 'http://bulksmsbd.net/api/smsapi');

        Http::fake(function (): never {
            throw new ConnectionException('Connection timed out');
        });

        $provider = new BulkSmsBdProvider();
        $result = $provider->send($this->makeCommunication('8801712345678'));

        $this->assertFalse($result->success);
        $this->assertSame('exception', $result->responseCode);
        $this->assertSame('failed', $result->status);
    }

    private function makeCommunication(string $recipient): Communication
    {
        return new Communication([
            'recipient' => $recipient,
            'body' => 'Test SMS body',
            'provider' => 'bulksmsbd',
        ]);
    }
}

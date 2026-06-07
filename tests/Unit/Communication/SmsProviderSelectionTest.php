<?php

declare(strict_types=1);

namespace Tests\Unit\Communication;

use App\Modules\Communication\Channels\SMS\Providers\BulkSmsBdProvider;
use App\Modules\Communication\Channels\SMS\Providers\TwilioSmsProvider;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Factories\CommunicationProviderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsProviderSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_resolves_bulksmsbd_when_default_provider_set(): void
    {
        config()->set('communication.default_providers.sms', 'bulksmsbd');

        $factory = new CommunicationProviderFactory([
            'sms' => [
                'twilio' => TwilioSmsProvider::class,
                'bulksmsbd' => BulkSmsBdProvider::class,
            ],
        ]);

        $provider = $factory->make(CommunicationChannel::Sms, null);

        $this->assertInstanceOf(BulkSmsBdProvider::class, $provider);
    }

    public function test_factory_resolves_explicit_provider_override(): void
    {
        config()->set('communication.default_providers.sms', 'twilio');

        $factory = new CommunicationProviderFactory([
            'sms' => [
                'twilio' => TwilioSmsProvider::class,
                'bulksmsbd' => BulkSmsBdProvider::class,
            ],
        ]);

        $provider = $factory->make(CommunicationChannel::Sms, 'bulksmsbd');

        $this->assertInstanceOf(BulkSmsBdProvider::class, $provider);
    }
}

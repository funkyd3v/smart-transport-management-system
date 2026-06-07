<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\SMS\Services;

use App\Modules\Communication\Channels\SMS\Contracts\SmsChannelInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Factories\CommunicationProviderFactory;
use App\Modules\Communication\Models\Communication;

class SmsChannelService implements SmsChannelInterface
{
    public function __construct(private readonly CommunicationProviderFactory $providerFactory) {}

    public function key(): string
    {
        return CommunicationChannel::Sms->value;
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        $provider = $this->providerFactory->make(CommunicationChannel::Sms, $communication->provider);

        return $provider->send($communication);
    }
}

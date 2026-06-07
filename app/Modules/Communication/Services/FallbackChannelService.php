<?php

declare(strict_types=1);

namespace App\Modules\Communication\Services;

use App\Modules\Communication\Contracts\CommunicationChannelInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Models\Communication;

class FallbackChannelService implements CommunicationChannelInterface
{
    public function __construct(private readonly string $channelKey) {}

    public function key(): string
    {
        return $this->channelKey;
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        return new CommunicationDispatchResultDTO(
            success: false,
            provider: $this->key(),
            providerMessageId: null,
            status: 'failed',
            responseCode: 'provider_not_configured',
            message: sprintf('No provider configured for [%s] channel.', $this->channelKey),
        );
    }
}

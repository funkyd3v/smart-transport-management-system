<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\Push\Providers;

use App\Modules\Communication\Channels\Push\Contracts\PushChannelInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Models\Communication;

class PushChannelService implements PushChannelInterface
{
    public function key(): string
    {
        return CommunicationChannel::Push->value;
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        return new CommunicationDispatchResultDTO(
            success: false,
            provider: $this->key(),
            providerMessageId: null,
            status: 'failed',
            responseCode: 'provider_not_configured',
            message: 'Push provider is not configured yet.',
        );
    }
}

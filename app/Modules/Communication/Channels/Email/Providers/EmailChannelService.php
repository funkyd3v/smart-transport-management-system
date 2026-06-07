<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\Email\Providers;

use App\Modules\Communication\Channels\Email\Contracts\EmailChannelInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Models\Communication;

class EmailChannelService implements EmailChannelInterface
{
    public function key(): string
    {
        return CommunicationChannel::Email->value;
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        return new CommunicationDispatchResultDTO(
            success: false,
            provider: $this->key(),
            providerMessageId: null,
            status: 'failed',
            responseCode: 'provider_not_configured',
            message: 'Email provider is not configured yet.',
        );
    }
}

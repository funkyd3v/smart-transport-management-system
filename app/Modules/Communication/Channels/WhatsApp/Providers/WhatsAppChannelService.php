<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\WhatsApp\Providers;

use App\Modules\Communication\Channels\WhatsApp\Contracts\WhatsAppChannelInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Models\Communication;

class WhatsAppChannelService implements WhatsAppChannelInterface
{
    public function key(): string
    {
        return CommunicationChannel::WhatsApp->value;
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        return new CommunicationDispatchResultDTO(
            success: false,
            providerMessageId: null,
            status: 'failed',
            message: 'WhatsApp provider is not configured yet.',
        );
    }
}

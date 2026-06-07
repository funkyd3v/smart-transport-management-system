<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\InApp\Services;

use App\Modules\Communication\Channels\InApp\Contracts\InAppChannelInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Models\Communication;

class InAppChannelService implements InAppChannelInterface
{
    public function key(): string
    {
        return CommunicationChannel::InApp->value;
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        return new CommunicationDispatchResultDTO(
            success: true,
            provider: $this->key(),
            providerMessageId: null,
            status: 'sent',
            responseCode: 'accepted',
            message: 'In-app communication recorded.',
            rawResponse: [],
        );
    }
}

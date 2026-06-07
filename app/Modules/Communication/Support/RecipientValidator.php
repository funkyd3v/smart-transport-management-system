<?php

declare(strict_types=1);

namespace App\Modules\Communication\Support;

use App\Modules\Communication\Enums\CommunicationChannel;

class RecipientValidator
{
    public function isValid(CommunicationChannel $channel, string $recipient): bool
    {
        return match ($channel) {
            CommunicationChannel::Sms, CommunicationChannel::WhatsApp => (bool) preg_match('/^\\+?[1-9]\\d{7,14}$/', $recipient),
            CommunicationChannel::Email => filter_var($recipient, FILTER_VALIDATE_EMAIL) !== false,
            default => $recipient !== '',
        };
    }
}

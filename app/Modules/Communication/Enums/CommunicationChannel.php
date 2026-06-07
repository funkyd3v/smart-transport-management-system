<?php

declare(strict_types=1);

namespace App\Modules\Communication\Enums;

enum CommunicationChannel: string
{
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';
    case Email = 'email';
    case Push = 'push';
    case InApp = 'in_app';
}

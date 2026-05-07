<?php

declare(strict_types=1);

namespace App\Modules\Trip\Enums;

enum NotificationType: string
{
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';
    case System = 'system';
}

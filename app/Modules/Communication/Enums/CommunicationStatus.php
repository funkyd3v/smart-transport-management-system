<?php

declare(strict_types=1);

namespace App\Modules\Communication\Enums;

enum CommunicationStatus: string
{
    case Queued = 'queued';
    case Sending = 'sending';
    case Sent = 'sent';
    case Failed = 'failed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}

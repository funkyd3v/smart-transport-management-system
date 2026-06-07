<?php

declare(strict_types=1);

namespace App\Modules\Payment\Enums;

enum PaymentStatus: string
{
    case Initiated = 'initiated';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}

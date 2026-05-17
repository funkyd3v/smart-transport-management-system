<?php

declare(strict_types=1);

namespace App\Modules\Cashbook\Enums;

enum CashbookType: string
{
    case Credit = 'credit';
    case Debit = 'debit';
}

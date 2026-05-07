<?php

declare(strict_types=1);

namespace App\Modules\Trip\Listeners;

use App\Modules\Trip\Events\PaymentRecorded;

class UpdateDueOnPayment
{
    public function handle(PaymentRecorded $event): void
    {
        // Due update is handled in PaymentService transaction.
        // Listener intentionally left as integration hook.
    }
}

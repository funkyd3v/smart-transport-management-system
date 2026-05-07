<?php

declare(strict_types=1);

namespace App\Modules\Trip\Listeners;

use App\Modules\Trip\Events\InvoiceGenerated;

class CreateDueRecordOnInvoice
{
    public function handle(InvoiceGenerated $event): void
    {
        // Due record is created in InvoiceService transaction.
        // Listener intentionally left as integration hook.
    }
}

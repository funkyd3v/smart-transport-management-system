<?php

namespace App\Modules\Invoice\Services;

use App\Modules\Invoice\Repositories\InvoiceRepositoryInterface;

class InvoiceService
{
    public function __construct(protected InvoiceRepositoryInterface $repository) {}
}

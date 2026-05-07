<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\GenerateInvoiceDTO;
use App\Modules\Trip\Models\Invoice;
use App\Modules\Trip\Services\InvoiceService;

class GenerateInvoiceAction
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function __invoke(GenerateInvoiceDTO $dto): Invoice
    {
        return $this->invoiceService->generate($dto);
    }
}

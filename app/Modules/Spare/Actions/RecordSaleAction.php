<?php

declare(strict_types=1);

namespace App\Modules\Spare\Actions;

use App\Modules\Spare\DTOs\RecordSaleDTO;
use App\Modules\Spare\Models\SpareSale;
use App\Modules\Spare\Services\SpareSaleService;

class RecordSaleAction
{
    public function __construct(private readonly SpareSaleService $service) {}

    public function __invoke(RecordSaleDTO $dto): SpareSale
    {
        return $this->service->recordSale($dto);
    }
}

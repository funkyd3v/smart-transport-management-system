<?php

declare(strict_types=1);

namespace App\Modules\Spare\Repositories;

use App\Modules\Spare\Models\SpareSale;
use Illuminate\Pagination\LengthAwarePaginator;

interface SpareSaleRepositoryInterface
{
    public function paginateSales(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function findSaleById(string $id): SpareSale;

    public function createSale(array $data): SpareSale;

    public function deleteSale(SpareSale $sale): bool;

    public function getSalesStats(array $filters = []): array;
}

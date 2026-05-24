<?php

namespace App\Modules\Spare\Repositories;

use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Models\SpareSaleType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SpareRepositoryInterface
{
    public function paginateInventory(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function findPartById(string $id): SparePart;

    public function findPartForUpdate(int $id): ?SparePart;

    public function createPart(array $data): SparePart;

    public function updatePart(SparePart $part, array $data): SparePart;

    public function savePart(SparePart $part): bool;

    public function deletePart(SparePart $part): bool;

    public function getInventoryStats(): array;

    public function categoryExists(int $id): bool;

    public function getCategories(): Collection;

    public function getSourceTrucks(): Collection;

    public function getSaleTypes(): Collection;

    public function getInStockParts(): Collection;

    public function findSaleTypeById(int $id): ?SpareSaleType;

    public function findSaleTypeByName(string $name): ?SpareSaleType;
}

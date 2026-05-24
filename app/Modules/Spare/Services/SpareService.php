<?php

namespace App\Modules\Spare\Services;

use App\Modules\Spare\DTOs\CreateSparePartDTO;
use App\Modules\Spare\DTOs\UpdateSparePartDTO;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Repositories\SpareRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class SpareService
{
    public function __construct(protected SpareRepositoryInterface $repository) {}

    public function inventoryPageData(array $filters): array
    {
        return [
            'stats' => $this->repository->getInventoryStats(),
            'parts' => $this->repository->paginateInventory($filters, 20),
            'categories' => $this->categories(),
            'filters' => $filters,
        ];
    }

    public function salesReferenceData(): array
    {
        return [
            'saleTypes' => $this->saleTypes(),
            'spareParts' => $this->repository->getInStockParts(),
        ];
    }

    public function categories(): Collection
    {
        return Cache::remember('spare_categories', 3600, fn (): Collection => $this->repository->getCategories());
    }

    public function sourceTrucks(): Collection
    {
        return $this->repository->getSourceTrucks();
    }

    public function saleTypes(): Collection
    {
        return Cache::remember('spare_sale_types', 3600, fn (): Collection => $this->repository->getSaleTypes());
    }

    public function findPartById(string $id): SparePart
    {
        return $this->repository->findPartById($id);
    }

    public function createPart(CreateSparePartDTO $dto): SparePart
    {
        $this->assertCategoryExists($dto->categoryId);

        $part = $this->repository->createPart([
            'category_id' => $dto->categoryId,
            'name' => $dto->name,
            'part_name' => $dto->name,
            'condition' => $dto->condition,
            'source_memo_number' => $dto->sourceMemoNumber,
            'source_truck_id' => $dto->sourceTruckId,
            'memo_number' => $dto->sourceMemoNumber,
            'sourced_from_truck_id' => $dto->sourceTruckId,
            'quantity' => $dto->quantity,
            'quantity_in_stock' => $dto->quantity,
            'purchase_price' => $dto->purchasePrice,
        ]);

        $this->forgetReferenceCache();

        return $part;
    }

    public function updatePart(SparePart $part, UpdateSparePartDTO $dto): SparePart
    {
        $this->assertCategoryExists($dto->categoryId);

        if ($dto->quantity < 0) {
            throw new RuntimeException('Stock quantity cannot be negative.');
        }

        $updated = $this->repository->updatePart($part, [
            'category_id' => $dto->categoryId,
            'name' => $dto->name,
            'part_name' => $dto->name,
            'condition' => $dto->condition,
            'source_memo_number' => $dto->sourceMemoNumber,
            'source_truck_id' => $dto->sourceTruckId,
            'memo_number' => $dto->sourceMemoNumber,
            'sourced_from_truck_id' => $dto->sourceTruckId,
            'quantity' => $dto->quantity,
            'quantity_in_stock' => $dto->quantity,
            'purchase_price' => $dto->purchasePrice,
        ]);

        $this->forgetReferenceCache();

        return $updated;
    }

    public function deletePart(SparePart $part): bool
    {
        $deleted = $this->repository->deletePart($part);

        $this->forgetReferenceCache();

        return $deleted;
    }

    private function assertCategoryExists(int $categoryId): void
    {
        if (! $this->repository->categoryExists($categoryId)) {
            throw new RuntimeException('Selected spare category is invalid.');
        }
    }

    private function forgetReferenceCache(): void
    {
        Cache::forget('spare_categories');
        Cache::forget('spare_sale_types');
    }
}

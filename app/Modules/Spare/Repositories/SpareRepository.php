<?php

namespace App\Modules\Spare\Repositories;

use App\Modules\Spare\Models\SpareCategory;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Models\SpareSaleType;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SpareRepository implements SpareRepositoryInterface
{
    public function paginateInventory(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return SparePart::query()
            ->select([
                'id',
                'ulid',
                'category_id',
                'name',
                'condition',
                'source_memo_number',
                'source_truck_id',
                'quantity',
                'purchase_price',
                'created_at',
            ])
            ->with([
                'category:id,name',
                'sourceTruck:id,truck_number',
            ])
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $search = (string) $filters['search'];
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(filled($filters['category_id'] ?? null), fn (Builder $query): Builder => $query->where('category_id', (int) $filters['category_id']))
            ->when(filled($filters['condition'] ?? null), fn (Builder $query): Builder => $query->where('condition', (string) $filters['condition']))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findPartById(string $id): SparePart
    {
        return SparePart::query()
            ->with(['category:id,name', 'sourceTruck:id,truck_number'])
            ->findOrFail($id);
    }

    public function findPartForUpdate(int $id): ?SparePart
    {
        return SparePart::query()->lockForUpdate()->find($id);
    }

    public function createPart(array $data): SparePart
    {
        return SparePart::query()->create($data);
    }

    public function updatePart(SparePart $part, array $data): SparePart
    {
        $part->fill($data);
        $part->save();

        return $part->refresh();
    }

    public function savePart(SparePart $part): bool
    {
        return $part->save();
    }

    public function deletePart(SparePart $part): bool
    {
        return (bool) $part->delete();
    }

    public function getInventoryStats(): array
    {
        return [
            'total_parts' => SparePart::query()->count(),
            'total_categories' => SpareCategory::query()->count(),
            'low_stock_items' => SparePart::query()->where('quantity', '<=', 3)->count(),
            'total_inventory_value' => (float) (SparePart::query()->selectRaw('SUM(quantity * purchase_price) as total')->value('total') ?? 0),
        ];
    }

    public function categoryExists(int $id): bool
    {
        return SpareCategory::query()->whereKey($id)->exists();
    }

    public function getCategories(): Collection
    {
        return SpareCategory::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    public function getSourceTrucks(): Collection
    {
        return Truck::query()
            ->select(['id', 'truck_number'])
            ->orderBy('truck_number')
            ->get();
    }

    public function getSaleTypes(): Collection
    {
        return SpareSaleType::query()
            ->select(['id', 'name', 'description'])
            ->orderBy('name')
            ->get();
    }

    public function getInStockParts(): Collection
    {
        return SparePart::query()
            ->select(['id', 'name', 'quantity', 'purchase_price'])
            ->inStock()
            ->orderBy('name')
            ->get();
    }

    public function findSaleTypeById(int $id): ?SpareSaleType
    {
        return SpareSaleType::query()->find($id);
    }

    public function findSaleTypeByName(string $name): ?SpareSaleType
    {
        return SpareSaleType::query()->where('name', $name)->first();
    }
}

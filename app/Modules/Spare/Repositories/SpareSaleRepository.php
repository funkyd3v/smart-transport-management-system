<?php

declare(strict_types=1);

namespace App\Modules\Spare\Repositories;

use App\Modules\Spare\Models\SpareSale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SpareSaleRepository implements SpareSaleRepositoryInterface
{
    public function paginateSales(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return SpareSale::query()
            ->select([
                'id',
                'ulid',
                'sale_type_id',
                'spare_part_id',
                'buyer_name',
                'quantity',
                'sale_price',
                'purchase_price_snapshot',
                'profit',
                'sold_at',
                'created_by',
                'created_at',
            ])
            ->with([
                'saleType:id,name',
                'sparePart:id,name',
                'creator:id,name',
            ])
            ->when(filled($filters['sale_type_id'] ?? null), fn (Builder $query): Builder => $query->where('sale_type_id', (int) $filters['sale_type_id']))
            ->when(filled($filters['from'] ?? null), fn (Builder $query): Builder => $query->whereDate('sold_at', '>=', (string) $filters['from']))
            ->when(filled($filters['to'] ?? null), fn (Builder $query): Builder => $query->whereDate('sold_at', '<=', (string) $filters['to']))
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function (Builder $subQuery) use ($search): void {
                    $subQuery->where('buyer_name', 'like', "%{$search}%")
                        ->orWhereHas('sparePart', fn (Builder $partQuery): Builder => $partQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('sold_at')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findSaleById(string $id): SpareSale
    {
        return SpareSale::query()
            ->with([
                'saleType:id,name,description',
                'sparePart:id,name,quantity,purchase_price',
                'creator:id,name',
            ])
            ->findOrFail($id);
    }

    public function createSale(array $data): SpareSale
    {
        return SpareSale::query()->create($data);
    }

    public function deleteSale(SpareSale $sale): bool
    {
        return (bool) $sale->delete();
    }

    public function getSalesStats(array $filters = []): array
    {
        $baseQuery = SpareSale::query()
            ->when(filled($filters['sale_type_id'] ?? null), fn (Builder $query): Builder => $query->where('sale_type_id', (int) $filters['sale_type_id']))
            ->when(filled($filters['from'] ?? null), fn (Builder $query): Builder => $query->whereDate('sold_at', '>=', (string) $filters['from']))
            ->when(filled($filters['to'] ?? null), fn (Builder $query): Builder => $query->whereDate('sold_at', '<=', (string) $filters['to']));

        $monthQuery = (clone $baseQuery)
            ->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year);

        return [
            'total_sales' => (clone $baseQuery)->count(),
            'total_revenue' => (float) ((clone $baseQuery)->sum('sale_price') ?? 0),
            'total_profit' => (float) ((clone $baseQuery)->sum('profit') ?? 0),
            'this_month_sales' => (clone $monthQuery)->count(),
        ];
    }
}

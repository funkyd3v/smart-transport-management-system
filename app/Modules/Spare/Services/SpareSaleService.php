<?php

declare(strict_types=1);

namespace App\Modules\Spare\Services;

use App\Modules\Cashbook\Enums\CashbookType;
use App\Modules\Cashbook\Services\CashbookService;
use App\Modules\Spare\DTOs\RecordSaleDTO;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Models\SpareSale;
use App\Modules\Spare\Repositories\SpareRepositoryInterface;
use App\Modules\Spare\Repositories\SpareSaleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SpareSaleService
{
    public function __construct(
        private readonly SpareSaleRepositoryInterface $saleRepository,
        private readonly SpareRepositoryInterface $spareRepository,
        private readonly CashbookService $cashbookService,
    ) {}

    public function salesPageData(array $filters): array
    {
        return [
            'stats' => $this->saleRepository->getSalesStats($filters),
            'sales' => $this->saleRepository->paginateSales($filters, 20),
            'filters' => $filters,
        ];
    }

    public function findSaleById(string $id): SpareSale
    {
        return $this->saleRepository->findSaleById($id);
    }

    public function deleteSale(SpareSale $sale): bool
    {
        return $this->saleRepository->deleteSale($sale);
    }

    public function recordSale(RecordSaleDTO $dto): SpareSale
    {
        $saleType = $this->spareRepository->findSaleTypeById($dto->saleTypeId);

        if ($saleType === null) {
            throw new RuntimeException('Selected sale type is invalid.');
        }

        return DB::transaction(function () use ($dto, $saleType): SpareSale {
            $part = null;
            $quantity = $dto->quantity;
            $purchasePriceSnapshot = 0.0;

            if ($saleType->name === 'spare_part') {
                if ($dto->sparePartId === null || $quantity === null) {
                    throw new RuntimeException('Spare part and quantity are required for this sale type.');
                }

                $part = $this->spareRepository->findPartForUpdate($dto->sparePartId);

                if (! $part instanceof SparePart) {
                    throw new RuntimeException('Selected spare part was not found.');
                }

                if ($part->quantity < $quantity) {
                    throw new RuntimeException('Insufficient stock for the selected spare part.');
                }

                $purchasePriceSnapshot = (float) $part->purchase_price;
                $part->quantity = $part->quantity - $quantity;
                $this->spareRepository->savePart($part);
            }

            $profit = (float) ($dto->salePrice - $purchasePriceSnapshot);

            $saleQuantity = $saleType->name === 'spare_part' ? $quantity : null;

            $sale = $this->saleRepository->createSale([
                'sale_type_id' => $dto->saleTypeId,
                'spare_part_id' => $part?->id,
                'buyer_name' => $dto->buyerName,
                'quantity' => $saleQuantity,
                'quantity_sold' => $saleQuantity ?? 0,
                'sale_price' => $dto->salePrice,
                'purchase_price_snapshot' => $purchasePriceSnapshot,
                'profit' => $profit,
                'note' => $dto->note,
                'sold_at' => $dto->soldAt,
                'sale_date' => $dto->soldAt,
                'created_by' => $dto->createdBy,
                'sold_by' => $dto->createdBy,
            ]);

            $this->cashbookService->record([
                'reference_id' => $sale->ulid,
                'reference_type' => 'spare_sale',
                'type' => CashbookType::Credit,
                'amount' => $dto->salePrice,
                'description' => 'Spare sale recorded for '.$dto->buyerName,
                'entry_date' => $dto->soldAt,
                'recorded_by' => $dto->createdBy,
                'note' => $dto->note,
            ]);

            return $sale->fresh(['saleType', 'sparePart', 'creator']);
        });
    }
}

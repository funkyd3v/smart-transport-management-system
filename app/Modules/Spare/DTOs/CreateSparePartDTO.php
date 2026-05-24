<?php

declare(strict_types=1);

namespace App\Modules\Spare\DTOs;

use App\Modules\Spare\Requests\StoreSparePartRequest;

readonly class CreateSparePartDTO
{
    public function __construct(
        public int $categoryId,
        public string $name,
        public string $condition,
        public ?string $sourceMemoNumber,
        public ?int $sourceTruckId,
        public int $quantity,
        public float $purchasePrice,
    ) {}

    public static function fromRequest(StoreSparePartRequest $request): self
    {
        $data = $request->validated();

        return new self(
            categoryId: (int) $data['category_id'],
            name: (string) $data['name'],
            condition: (string) $data['condition'],
            sourceMemoNumber: $data['source_memo_number'] ?? null,
            sourceTruckId: isset($data['source_truck_id']) ? (int) $data['source_truck_id'] : null,
            quantity: (int) $data['quantity'],
            purchasePrice: (float) $data['purchase_price'],
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Spare\DTOs;

use App\Modules\Spare\Requests\RecordSaleRequest;

readonly class RecordSaleDTO
{
    public function __construct(
        public int $saleTypeId,
        public ?int $sparePartId,
        public string $buyerName,
        public ?int $quantity,
        public float $salePrice,
        public string $soldAt,
        public ?string $note,
        public int $createdBy,
    ) {}

    public static function fromRequest(RecordSaleRequest $request): self
    {
        $data = $request->validated();

        return new self(
            saleTypeId: (int) $data['sale_type_id'],
            sparePartId: isset($data['spare_part_id']) ? (int) $data['spare_part_id'] : null,
            buyerName: (string) $data['buyer_name'],
            quantity: isset($data['quantity']) ? (int) $data['quantity'] : null,
            salePrice: (float) $data['sale_price'],
            soldAt: (string) $data['sold_at'],
            note: $data['note'] ?? null,
            createdBy: (int) $request->user()->id,
        );
    }
}

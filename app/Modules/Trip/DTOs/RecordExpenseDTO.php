<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use App\Modules\Trip\Http\Requests\RecordExpenseRequest;

readonly class RecordExpenseDTO
{
    public function __construct(
        public string $tripUlid,
        public int $categoryId,
        public int $recordedBy,
        public float $amount,
        public ?string $description,
        public string $expenseDate,
        public ?string $receiptPath,
    ) {}

    public static function fromRequest(RecordExpenseRequest $request): self
    {
        $data = $request->validated();

        return new self(
            tripUlid: (string) $data['trip_ulid'],
            categoryId: (int) $data['category_id'],
            recordedBy: (int) $request->user()->id,
            amount: (float) $data['amount'],
            description: $data['description'] ?? null,
            expenseDate: (string) $data['expense_date'],
            receiptPath: $data['receipt_path'] ?? null,
        );
    }
}

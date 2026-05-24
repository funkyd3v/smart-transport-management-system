<?php

declare(strict_types=1);

namespace App\Modules\Spare\Requests;

use App\Modules\Spare\Models\SpareSaleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RecordSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_type_id' => ['required', 'integer', 'exists:spare_sale_types,id'],
            'spare_part_id' => ['nullable', 'integer', 'exists:spare_parts,id'],
            'buyer_name' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'sale_price' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'sold_at' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->isSparePartType()) {
                    return;
                }

                if (! $this->filled('spare_part_id')) {
                    $validator->errors()->add('spare_part_id', 'Spare part is required for spare part sales.');
                }

                if (! $this->filled('quantity')) {
                    $validator->errors()->add('quantity', 'Quantity is required for spare part sales.');
                }
            },
        ];
    }

    private function isSparePartType(): bool
    {
        $saleTypeId = $this->input('sale_type_id');

        if (! is_numeric($saleTypeId)) {
            return false;
        }

        $saleType = SpareSaleType::query()->find((int) $saleTypeId);

        return $saleType?->name === 'spare_part';
    }
}

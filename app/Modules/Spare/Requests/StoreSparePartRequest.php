<?php

declare(strict_types=1);

namespace App\Modules\Spare\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSparePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:spare_categories,id'],
            'condition' => ['required', 'in:new,old'],
            'source_memo_number' => ['nullable', 'required_if:condition,new', 'string', 'max:100'],
            'source_truck_id' => ['nullable', 'required_if:condition,old', 'integer', 'exists:trucks,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }
}

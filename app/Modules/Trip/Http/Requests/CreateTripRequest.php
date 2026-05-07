<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'truck_id' => ['required', 'integer', 'exists:trucks,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'status_id' => ['required', 'integer', 'exists:trip_statuses,id'],
            'pickup_point' => ['required', 'string', 'max:300'],
            'delivery_point' => ['required', 'string', 'max:300'],
            'route_description' => ['nullable', 'string'],
            'goods_description' => ['nullable', 'string'],
            'load_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:load_date'],
            'trip_rate' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'advance_payment' => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'notes' => ['nullable', 'string'],
            'sms_note' => ['nullable', 'string'],
            'goods' => ['required', 'array', 'min:1'],
            'goods.*.item_name' => ['required', 'string', 'max:200'],
            'goods.*.unit' => ['nullable', 'string', 'max:50'],
            'goods.*.quantity' => ['required', 'numeric', 'gt:0'],
            'goods.*.unit_price' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }
}

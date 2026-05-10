<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Trip;

use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Truck\Models\Truck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (! is_string($value)) {
                $sanitized[$key] = $value;

                continue;
            }

            $sanitized[$key] = strip_tags(trim($value));
        }

        if ($this->filled('load_datetime') && ! $this->filled('load_date')) {
            $sanitized['load_date'] = (string) $this->input('load_datetime');
        }

        if (is_array($this->input('goods'))) {
            $goods = [];

            foreach ((array) $this->input('goods') as $item) {
                $goods[] = [
                    'item_name' => isset($item['description']) ? strip_tags(trim((string) $item['description'])) : strip_tags(trim((string) ($item['item_name'] ?? ''))),
                    'unit' => isset($item['unit']) ? strip_tags(trim((string) $item['unit'])) : '',
                    'quantity' => $item['quantity'] ?? null,
                    'unit_price' => $item['unit_price'] ?? null,
                ];
            }

            $sanitized['goods'] = $goods;
        }

        $this->merge($sanitized);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')],
            'truck_id' => ['required', 'integer', Rule::exists('trucks', 'id')],
            'driver_id' => ['required', 'integer', Rule::exists('drivers', 'id')],
            'pickup_point' => ['required', 'string', 'max:300'],
            'delivery_point' => ['required', 'string', 'max:300'],
            'load_datetime' => ['required_without:load_date', 'date'],
            'load_date' => ['required_without:load_datetime', 'date'],
            'trip_rate' => ['required', 'numeric', 'min:0'],
            'advance_payment' => ['nullable', 'numeric', 'min:0', 'lte:trip_rate'],
            'route_description' => ['nullable', 'string'],
            'goods_description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'sms_note' => ['nullable', 'string'],
            'goods' => ['required', 'array', 'min:1'],
            'goods.*.item_name' => ['required', 'string', 'max:200'],
            'goods.*.unit' => ['required', 'string', 'max:50'],
            'goods.*.quantity' => ['required', 'numeric', 'min:1'],
            'goods.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $client = Client::query()->with('user:id,is_active')->find((int) $this->input('client_id'));

            if ($client === null || ! $client->user?->is_active) {
                $validator->errors()->add('client_id', 'Selected client is not active.');
            }

            $driver = Driver::query()->with('user:id,is_active,approved_at')->find((int) $this->input('driver_id'));

            if ($driver === null || ! $driver->user?->is_active || $driver->user?->approved_at === null) {
                $validator->errors()->add('driver_id', 'Selected driver must be active and approved.');
            }

            $truck = Truck::query()->with('status:id,name')->find((int) $this->input('truck_id'));

            if ($truck !== null) {
                $statusName = strtolower(trim((string) ($truck->status?->name ?? 'idle')));
                $normalized = in_array($statusName, ['on_trip', 'on trip', 'in transit', 'ontrip'], true)
                    ? 'on_trip'
                    : (in_array($statusName, ['under_workshop', 'under workshop', 'under maintenance', 'under_maintenance', 'workshop'], true) ? 'under_workshop' : 'idle');

                if ($normalized !== 'idle') {
                    $validator->errors()->add('truck_id', 'Selected truck must be idle.');
                }
            }
        });
    }
}

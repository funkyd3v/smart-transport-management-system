<?php

declare(strict_types=1);

namespace App\Modules\Payment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiateBkashPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('client');
    }

    public function rules(): array
    {
        return [
            'trip_ulid' => ['required', 'string', 'exists:trips,ulid'],
            'amount' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}

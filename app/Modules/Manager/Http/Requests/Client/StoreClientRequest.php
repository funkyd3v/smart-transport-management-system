<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(array_map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value, $this->all()));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'client_type' => ['required', 'in:port,contractual,mega_project'],
            'project' => ['required_if:client_type,contractual,mega_project', 'nullable', 'string', 'max:255'],
            'project_agreement_number' => ['required_if:client_type,contractual,mega_project', 'nullable', 'string', 'max:100'],
            'project_value' => ['required_if:client_type,contractual,mega_project', 'nullable', 'numeric', 'min:0'],
            'target_finishing_date' => ['required_if:client_type,contractual,mega_project', 'nullable', 'date', 'after_or_equal:today'],
            'status' => ['sometimes', 'in:active,inactive'],
        ];
    }
}

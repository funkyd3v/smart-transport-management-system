<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Client;

use App\Modules\Client\Models\Client;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = array_map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value, $this->all());

        if (empty($sanitized['phone_number']) && ! empty($sanitized['contact_number'])) {
            $sanitized['phone_number'] = $sanitized['contact_number'];
        }

        $this->merge($sanitized);
    }

    public function rules(): array
    {
        $client = $this->route('client');
        $ignoreUserId = $client instanceof Client ? (int) ($client->user_id ?? 0) : 0;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'regex:/^01[3-9]\d{8}$/', Rule::unique('users', 'phone')->ignore($ignoreUserId)],
            'client_type' => ['required', 'in:port,contractual,mega_project'],
            'project' => ['required_if:client_type,contractual,mega_project', 'nullable', 'string', 'max:255'],
            'project_agreement_number' => ['required_if:client_type,contractual,mega_project', 'nullable', 'string', 'max:100'],
            'project_value' => ['required_if:client_type,contractual,mega_project', 'nullable', 'numeric', 'min:0'],
            'target_finishing_date' => [
                'required_if:client_type,contractual,mega_project',
                'nullable',
                'date',
                $this->targetDateRule(),
            ],
            'status' => ['sometimes', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Phone number must be a valid Bangladeshi number (01XXXXXXXXX).',
            'project.required_if' => 'Project is required for contractual and mega project clients.',
            'project_agreement_number.required_if' => 'Project agreement number is required for contractual and mega project clients.',
            'project_value.required_if' => 'Project value is required for contractual and mega project clients.',
            'target_finishing_date.required_if' => 'Target finishing date is required for contractual and mega project clients.',
        ];
    }

    private function targetDateRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $client = $this->route('client');

            if (! $client instanceof Client || blank($value)) {
                return;
            }

            $currentDate = blank($client->target_finishing_date)
                ? null
                : (string) $client->target_finishing_date;
            $incomingDate = (string) $value;

            if ($incomingDate !== $currentDate && $incomingDate < now()->toDateString()) {
                $fail('The target finishing date must be today or a future date.');
            }
        };
    }
}

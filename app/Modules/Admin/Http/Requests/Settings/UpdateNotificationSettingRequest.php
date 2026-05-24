<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests\Settings;

use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class UpdateNotificationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sms_enabled' => ['nullable', 'boolean'],
            'sms_provider' => ['nullable', 'string', Rule::in(['twilio', 'nexmo', 'custom'])],
            'sms_api_key' => ['nullable', 'string'],
            'sms_sender_name' => ['nullable', 'string', 'max:11'],
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_api_key' => ['nullable', 'string'],
            'whatsapp_sender_number' => ['nullable', 'string'],
            'low_stock_alert_enabled' => ['nullable', 'boolean'],
            'due_payment_alert_enabled' => ['nullable', 'boolean'],
            'trip_status_alert_enabled' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $data = $this->all();

            $smsEnabled = filter_var($data['sms_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $whatsappEnabled = filter_var($data['whatsapp_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($smsEnabled) {
                if (blank($data['sms_provider'] ?? null)) {
                    $validator->errors()->add('sms_provider', 'SMS provider is required when SMS is enabled.');
                }
                $hasStoredSmsApiKey = filled(SystemSetting::getValue('notification', 'sms_api_key'));
                if (blank($data['sms_api_key'] ?? null) && ! $hasStoredSmsApiKey) {
                    $validator->errors()->add('sms_api_key', 'SMS API key is required when SMS is enabled.');
                }
                if (blank($data['sms_sender_name'] ?? null)) {
                    $validator->errors()->add('sms_sender_name', 'SMS sender name is required when SMS is enabled.');
                }
            }

            if ($whatsappEnabled) {
                $hasStoredWhatsappApiKey = filled(SystemSetting::getValue('notification', 'whatsapp_api_key'));
                if (blank($data['whatsapp_api_key'] ?? null) && ! $hasStoredWhatsappApiKey) {
                    $validator->errors()->add('whatsapp_api_key', 'WhatsApp API key is required when WhatsApp is enabled.');
                }
                if (blank($data['whatsapp_sender_number'] ?? null)) {
                    $validator->errors()->add('whatsapp_sender_number', 'WhatsApp sender number is required when WhatsApp is enabled.');
                }
            }
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\SMS\Providers;

use App\Modules\Communication\Channels\SMS\Contracts\SmsProviderInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Models\Communication;
use Illuminate\Support\Facades\Http;
use Throwable;

class BulkSmsBdProvider implements SmsProviderInterface
{
    public function key(): string
    {
        return 'bulksmsbd';
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        $apiKey = (string) config('communication.providers.sms.bulksmsbd.api_key');
        $senderId = (string) config('communication.providers.sms.bulksmsbd.sender_id');
        $endpoint = (string) config('communication.providers.sms.bulksmsbd.endpoint', 'http://bulksmsbd.net/api/smsapi');
        $timeout = (int) config('communication.providers.sms.bulksmsbd.timeout', 10);
        $connectTimeout = (int) config('communication.providers.sms.bulksmsbd.connect_timeout', 5);
        $retryTimes = (int) config('communication.providers.sms.bulksmsbd.retry_times', 2);
        $retrySleepMs = (int) config('communication.providers.sms.bulksmsbd.retry_sleep_ms', 250);

        if ($apiKey === '' || $senderId === '') {
            return new CommunicationDispatchResultDTO(
                success: false,
                provider: $this->key(),
                providerMessageId: null,
                status: 'failed',
                responseCode: 'config_missing',
                message: 'BulkSMSBD credentials are not configured.',
            );
        }

        try {
            $response = Http::asForm()
                ->timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->retry($retryTimes, $retrySleepMs, throw: false)
                ->post($endpoint, [
                    'api_key' => $apiKey,
                    'type' => 'text',
                    'number' => $this->normalizeNumber($communication->recipient),
                    'senderid' => $senderId,
                    'message' => $communication->body,
                ]);

            $json = $response->json();
            $responseCode = is_array($json) ? (string) ($json['response_code'] ?? $response->status()) : (string) $response->status();
            $isSuccess = $responseCode === '202';

            return new CommunicationDispatchResultDTO(
                success: $isSuccess,
                provider: $this->key(),
                providerMessageId: is_array($json) ? ($json['message_id'] ?? $json['smsid'] ?? null) : null,
                status: $isSuccess ? 'sent' : 'failed',
                responseCode: $responseCode,
                message: $isSuccess ? 'Message sent via BulkSMSBD.' : $this->resolveFailureMessage($json),
                rawResponse: is_array($json) ? $json : ['response' => $response->body()],
            );
        } catch (Throwable $e) {
            return new CommunicationDispatchResultDTO(
                success: false,
                provider: $this->key(),
                providerMessageId: null,
                status: 'failed',
                responseCode: 'exception',
                message: 'BulkSMSBD send exception.',
                rawResponse: [
                    'error' => $e->getMessage(),
                ],
            );
        }
    }

    private function normalizeNumber(string $recipient): string
    {
        $normalized = preg_replace('/[^0-9+]/', '', $recipient) ?? $recipient;

        if (str_starts_with($normalized, '+')) {
            return ltrim($normalized, '+');
        }

        if (str_starts_with($normalized, '0')) {
            return '88'.$normalized;
        }

        return $normalized;
    }

    private function resolveFailureMessage(mixed $json): string
    {
        if (! is_array($json)) {
            return 'BulkSMSBD send failed.';
        }

        $error = (string) ($json['error_message'] ?? '');

        if ($error !== '') {
            return $error;
        }

        return 'BulkSMSBD send failed.';
    }
}

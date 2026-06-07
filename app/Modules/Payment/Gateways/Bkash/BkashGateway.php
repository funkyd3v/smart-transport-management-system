<?php

declare(strict_types=1);

namespace App\Modules\Payment\Gateways\Bkash;

use App\Modules\Payment\DTOs\GatewayResponseDTO;
use App\Modules\Payment\DTOs\PaymentExecutionResultDTO;
use App\Modules\Payment\DTOs\PaymentInitiationResultDTO;
use App\Modules\Payment\DTOs\PaymentTokenDTO;
use App\Modules\Payment\DTOs\PaymentValidationResultDTO;
use App\Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class BkashGateway implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'bkash';
    }

    public function initiate(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        try {
            return $this->createPayment($payment, $payload)->toGatewayResponse();
        } catch (Throwable $throwable) {
            return $this->failureResponse($payment, $throwable->getMessage(), 'create_payment_failed');
        }
    }

    public function validate(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        try {
            $paymentId = (string) ($payload['payment_id'] ?? $payment->provider_reference ?? '');

            if ($paymentId === '') {
                return $this->failureResponse($payment, 'Missing bKash payment ID.', 'missing_payment_id');
            }

            $result = $this->queryPayment($paymentId);

            if (! $result->success) {
                return $result->toGatewayResponse($paymentId);
            }

            $expectedAmount = $this->formatAmount((float) $payment->amount);

            if ($result->amount !== null && $result->amount !== $expectedAmount) {
                return new GatewayResponseDTO(
                    success: false,
                    status: 'failed',
                    gatewayTransactionId: $result->transactionId ?? $paymentId,
                    providerReference: $paymentId,
                    message: 'bKash payment amount mismatch.',
                    rawResponse: $result->rawResponse,
                );
            }

            if ($result->currency !== null && Str::upper($result->currency) !== Str::upper($this->config()['currency'])) {
                return new GatewayResponseDTO(
                    success: false,
                    status: 'failed',
                    gatewayTransactionId: $result->transactionId ?? $paymentId,
                    providerReference: $paymentId,
                    message: 'bKash payment currency mismatch.',
                    rawResponse: $result->rawResponse,
                );
            }

            return $result->toGatewayResponse($paymentId);
        } catch (Throwable $throwable) {
            return $this->failureResponse($payment, $throwable->getMessage(), 'query_payment_failed');
        }
    }

    public function cancel(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: true,
            status: 'cancelled',
            gatewayTransactionId: $payload['gateway_transaction_id'] ?? null,
            providerReference: $payload['provider_reference'] ?? $payment->provider_reference,
            message: 'bKash payment cancelled.',
            rawResponse: $payload,
        );
    }

    public function grantToken(): PaymentTokenDTO
    {
        $config = $this->config();

        $response = Http::acceptJson()
            ->asJson()
            ->timeout((int) $config['timeout'])
            ->withHeaders([
                'username' => $config['username'],
                'password' => $config['password'],
            ])
            ->post($this->url('/checkout/token/grant'), [
                'app_key' => $config['app_key'],
                'app_secret' => $config['app_secret'],
            ]);

        if (! $response->successful()) {
            return new PaymentTokenDTO(
                success: false,
                tokenType: null,
                accessToken: null,
                refreshToken: null,
                expiresIn: null,
                message: $this->extractErrorMessage($response->json(), 'Unable to grant bKash access token.'),
                rawResponse: $response->json() ?? [],
            );
        }

        $token = new PaymentTokenDTO(
            success: true,
            tokenType: (string) $response->json('token_type', 'Bearer'),
            accessToken: (string) $response->json('id_token', ''),
            refreshToken: $this->stringify($response->json('refresh_token')),
            expiresIn: (int) $response->json('expires_in', 3600),
            message: 'bKash access token granted.',
            rawResponse: $response->json() ?? [],
        );

        if ($token->accessToken !== null && $token->accessToken !== '') {
            Cache::put(
                $this->tokenCacheKey(),
                $token->rawResponse,
                now()->addSeconds(max(($token->expiresIn ?? 3600) - 60, 60)),
            );
        }

        return $token;
    }

    public function createPayment(Payment $payment, array $payload = []): PaymentInitiationResultDTO
    {
        $config = $this->config();
        $token = $this->accessToken();
        $merchantInvoiceNumber = $this->merchantInvoiceNumber($payment, $payload);
        $callbackUrl = (string) ($payload['callback_url'] ?? $config['callback_url']);
        $payerReference = $this->sanitizeReference((string) ($payload['payer_reference'] ?? $payment->transaction_reference ?? $payment->ulid));

        $response = Http::acceptJson()
            ->asJson()
            ->timeout((int) $config['timeout'])
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $config['app_key'],
            ])
            ->post($this->url('/tokenized/checkout/payment/create'), [
                'mode' => (string) ($payload['mode'] ?? $config['mode']),
                'payerReference' => $payerReference,
                'callbackURL' => $callbackUrl,
                'amount' => $this->formatAmount((float) $payment->amount),
                'currency' => (string) ($payload['currency'] ?? $config['currency']),
                'intent' => (string) ($payload['intent'] ?? $config['intent']),
                'merchantInvoiceNumber' => $merchantInvoiceNumber,
                'merchantAssociationInfo' => $payload['merchant_association_info'] ?? null,
            ]);

        if (! $response->successful()) {
            return new PaymentInitiationResultDTO(
                success: false,
                status: 'failed',
                paymentId: null,
                redirectUrl: null,
                callbackUrl: $callbackUrl,
                merchantInvoiceNumber: $merchantInvoiceNumber,
                message: $this->extractErrorMessage($response->json(), 'Unable to create bKash payment.'),
                rawResponse: $response->json() ?? [],
            );
        }

        $paymentId = (string) $response->json('paymentID', '');
        $redirectUrl = (string) $response->json('bkashURL', '');

        return new PaymentInitiationResultDTO(
            success: $paymentId !== '',
            status: 'initiated',
            paymentId: $paymentId !== '' ? $paymentId : null,
            redirectUrl: $redirectUrl !== '' ? $redirectUrl : null,
            callbackUrl: $callbackUrl,
            merchantInvoiceNumber: $merchantInvoiceNumber,
            message: $paymentId !== '' ? 'bKash payment created.' : 'bKash payment creation response missing payment ID.',
            rawResponse: $response->json() ?? [],
        );
    }

    public function executePayment(Payment $payment, string $paymentId, array $payload = []): PaymentExecutionResultDTO
    {
        $config = $this->config();
        $token = $this->accessToken();

        $response = Http::acceptJson()
            ->timeout((int) $config['timeout'])
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $config['app_key'],
            ])
            ->post($this->url('/tokenized/checkout/execute/'.$paymentId), []);

        if (! $response->successful()) {
            return new PaymentExecutionResultDTO(
                success: false,
                status: 'failed',
                paymentId: $paymentId,
                transactionId: null,
                customerMsisdn: null,
                amount: $this->formatAmount((float) $payment->amount),
                currency: (string) $config['currency'],
                merchantInvoiceNumber: $this->merchantInvoiceNumber($payment, $payload),
                message: $this->extractErrorMessage($response->json(), 'Unable to execute bKash payment.'),
                rawResponse: $response->json() ?? [],
            );
        }

        $status = $this->normalizeBikashStatus((string) $response->json('transactionStatus', ''));
        $transactionId = $this->stringify($response->json('trxID'));

        return new PaymentExecutionResultDTO(
            success: $status === 'succeeded',
            status: $status,
            paymentId: (string) $response->json('paymentID', $paymentId),
            transactionId: $transactionId,
            customerMsisdn: $this->stringify($response->json('customerMsisdn')),
            amount: $this->stringify($response->json('amount')),
            currency: $this->stringify($response->json('currency')),
            merchantInvoiceNumber: $this->stringify($response->json('merchantInvoiceNumber')),
            message: $status === 'succeeded' ? 'bKash payment executed.' : 'bKash payment execution did not complete successfully.',
            rawResponse: $response->json() ?? [],
        );
    }

    public function queryPayment(string $paymentId): PaymentValidationResultDTO
    {
        $config = $this->config();
        $token = $this->accessToken();

        $response = Http::acceptJson()
            ->timeout((int) $config['timeout'])
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $config['app_key'],
            ])
            ->get($this->url('/checkout/payment/query/'.$paymentId));

        if (! $response->successful()) {
            return new PaymentValidationResultDTO(
                success: false,
                status: 'failed',
                paymentId: $paymentId,
                transactionId: null,
                transactionStatus: null,
                amount: null,
                currency: null,
                merchantInvoiceNumber: null,
                message: $this->extractErrorMessage($response->json(), 'Unable to query bKash payment.'),
                rawResponse: $response->json() ?? [],
            );
        }

        $transactionStatus = (string) $response->json('transactionStatus', '');
        $status = $this->normalizeBikashStatus($transactionStatus);
        $transactionId = $this->stringify(Arr::get($response->json(), 'trxID'));

        return new PaymentValidationResultDTO(
            success: $status === 'succeeded',
            status: $status,
            paymentId: (string) $response->json('paymentID', $paymentId),
            transactionId: $transactionId,
            transactionStatus: $transactionStatus !== '' ? $transactionStatus : null,
            amount: $this->stringify($response->json('amount')),
            currency: $this->stringify($response->json('currency')),
            merchantInvoiceNumber: $this->stringify($response->json('merchantInvoiceNumber')),
            message: $status === 'succeeded' ? 'bKash payment completed.' : 'bKash payment is not completed yet.',
            rawResponse: $response->json() ?? [],
        );
    }

    private function accessToken(): string
    {
        $cached = Cache::get($this->tokenCacheKey());

        if (is_array($cached) && isset($cached['id_token']) && $cached['id_token'] !== '') {
            return (string) $cached['id_token'];
        }

        $token = $this->grantToken();

        if (! $token->success || $token->accessToken === null || $token->accessToken === '') {
            throw new \RuntimeException($token->message);
        }

        return $token->accessToken;
    }

    private function config(): array
    {
        $config = (array) config('payment.bkash', []);

        if ($config === []) {
            throw new \RuntimeException('bKash payment configuration is missing.');
        }

        $baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');

        if ($baseUrl === '') {
            throw new \RuntimeException('bKash base URL is missing.');
        }

        return [
            'base_url' => $baseUrl,
            'app_key' => (string) ($config['app_key'] ?? ''),
            'app_secret' => (string) ($config['app_secret'] ?? ''),
            'username' => (string) ($config['username'] ?? ''),
            'password' => (string) ($config['password'] ?? ''),
            'callback_url' => (string) ($config['callback_url'] ?? ''),
            'mode' => (string) ($config['mode'] ?? '0011'),
            'intent' => (string) ($config['intent'] ?? 'authorization'),
            'currency' => (string) ($config['currency'] ?? 'BDT'),
            'merchant_invoice_prefix' => (string) ($config['merchant_invoice_prefix'] ?? 'BK-'),
            'timeout' => (int) ($config['timeout'] ?? 15),
        ];
    }

    private function url(string $path): string
    {
        return $this->config()['base_url'].$path;
    }

    private function merchantInvoiceNumber(Payment $payment, array $payload = []): string
    {
        $invoiceNumber = (string) ($payload['merchant_invoice_number'] ?? $payment->transaction_reference ?? $payment->ulid);

        return $this->sanitizeReference($invoiceNumber) !== ''
            ? $this->sanitizeReference($invoiceNumber)
            : $this->sanitizeReference($this->config()['merchant_invoice_prefix'].$payment->ulid);
    }

    private function sanitizeReference(string $value): string
    {
        return trim((string) preg_replace('/[^A-Za-z0-9._-]/', '', $value));
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    private function normalizeBikashStatus(string $status): string
    {
        return match (Str::lower(trim($status))) {
            'completed', 'success', 'succeeded' => 'succeeded',
            'initiated', 'pending', 'processing' => 'initiated',
            'cancelled', 'canceled' => 'cancelled',
            default => 'failed',
        };
    }

    private function extractErrorMessage(mixed $payload, string $fallback): string
    {
        if (! is_array($payload)) {
            return $fallback;
        }

        return (string) ($payload['errorMessage'] ?? $payload['statusMessage'] ?? $payload['message'] ?? $fallback);
    }

    private function stringify(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    private function tokenCacheKey(): string
    {
        return 'payment.bkash.token.'.sha1($this->config()['base_url']);
    }

    private function failureResponse(Payment $payment, string $message, string $status): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: false,
            status: $status,
            gatewayTransactionId: null,
            providerReference: $payment->provider_reference,
            message: $message,
            rawResponse: [
                'message' => $message,
                'status' => $status,
            ],
        );
    }
}

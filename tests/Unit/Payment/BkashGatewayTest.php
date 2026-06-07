<?php

declare(strict_types=1);

namespace Tests\Unit\Payment;

use App\Modules\Payment\Gateways\Bkash\BkashGateway;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BkashGatewayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_it_creates_a_bkash_payment_and_returns_an_initiated_response(): void
    {
        $this->configureBkash();

        Http::fake([
            'https://bkash.test/checkout/token/grant' => Http::response([
                'token_type' => 'Bearer',
                'id_token' => 'bkash-token',
                'expires_in' => 3600,
                'refresh_token' => 'refresh-token',
            ], 200),
            'https://bkash.test/tokenized/checkout/payment/create' => Http::response([
                'statusCode' => '0000',
                'statusMessage' => 'Successful',
                'paymentID' => 'TR001',
                'bkashURL' => 'https://bkash.test/checkout/TR001',
                'callbackURL' => 'https://app.test/bkash/callback',
                'transactionStatus' => 'Initiated',
                'amount' => '500.00',
                'currency' => 'BDT',
                'merchantInvoiceNumber' => 'BK-ULID123',
            ], 200),
        ]);

        $gateway = new BkashGateway();
        $payment = $this->makePayment(500.00);

        $response = $gateway->initiate($payment, [
            'payer_reference' => '01712345678',
        ]);

        $this->assertTrue($response->success);
        $this->assertSame('initiated', $response->status);
        $this->assertSame('TR001', $response->gatewayTransactionId);
        $this->assertSame('TR001', $response->providerReference);
        $this->assertSame('bKash payment created.', $response->message);
        $this->assertSame('https://bkash.test/checkout/TR001', $response->rawResponse['bkashURL']);
    }

    public function test_it_executes_a_bkash_payment_and_maps_the_transaction_id(): void
    {
        $this->configureBkash();

        Http::fake([
            'https://bkash.test/checkout/token/grant' => Http::response([
                'token_type' => 'Bearer',
                'id_token' => 'bkash-token',
                'expires_in' => 3600,
                'refresh_token' => 'refresh-token',
            ], 200),
            'https://bkash.test/tokenized/checkout/execute/TR001' => Http::response([
                'statusCode' => '0000',
                'statusMessage' => 'Successful',
                'paymentID' => 'TR001',
                'trxID' => 'TXN-999',
                'customerMsisdn' => '01712345678',
                'payerReference' => '01712345678',
                'paymentExecuteTime' => '2026-06-07T12:00:00:000 GMT+0600',
                'amount' => '500.00',
                'currency' => 'BDT',
                'transactionStatus' => 'Completed',
                'merchantInvoiceNumber' => 'BK-ULID123',
            ], 200),
        ]);

        $gateway = new BkashGateway();
        $payment = $this->makePayment(500.00);

        $response = $gateway->executePayment($payment, 'TR001');

        $this->assertTrue($response->success);
        $this->assertSame('succeeded', $response->status);
        $this->assertSame('TXN-999', $response->transactionId);
        $this->assertSame('TR001', $response->paymentId);
        $this->assertSame('bKash payment executed.', $response->message);
    }

    public function test_it_validates_a_completed_bkash_payment_and_rejects_amount_tampering(): void
    {
        $this->configureBkash();

        Http::fake([
            'https://bkash.test/checkout/token/grant' => Http::response([
                'token_type' => 'Bearer',
                'id_token' => 'bkash-token',
                'expires_in' => 3600,
                'refresh_token' => 'refresh-token',
            ], 200),
            'https://bkash.test/checkout/payment/query/TR001' => Http::response([
                'paymentID' => 'TR001',
                'createTime' => '2026-06-07T12:00:00:000 GMT+0600',
                'updateTime' => '2026-06-07T12:00:05:000 GMT+0600',
                'trxID' => 'TXN-999',
                'transactionStatus' => 'Completed',
                'amount' => '499.00',
                'currency' => 'BDT',
                'intent' => 'authorization',
                'merchantInvoiceNumber' => 'BK-ULID123',
                'refundAmount' => '0',
            ], 200),
        ]);

        $gateway = new BkashGateway();
        $payment = $this->makePayment(500.00);
        $payment->provider_reference = 'TR001';

        $response = $gateway->validate($payment, [
            'payment_id' => 'TR001',
        ]);

        $this->assertFalse($response->success);
        $this->assertSame('failed', $response->status);
        $this->assertSame('bKash payment amount mismatch.', $response->message);
        $this->assertSame('TXN-999', $response->gatewayTransactionId);
    }

    private function configureBkash(): void
    {
        config()->set('payment.bkash.base_url', 'https://bkash.test');
        config()->set('payment.bkash.app_key', 'app-key');
        config()->set('payment.bkash.app_secret', 'app-secret');
        config()->set('payment.bkash.username', 'merchant-user');
        config()->set('payment.bkash.password', 'merchant-pass');
        config()->set('payment.bkash.callback_url', 'https://app.test/bkash/callback');
        config()->set('payment.bkash.mode', '0011');
        config()->set('payment.bkash.intent', 'authorization');
        config()->set('payment.bkash.currency', 'BDT');
        config()->set('payment.bkash.merchant_invoice_prefix', 'BK-');
        config()->set('payment.bkash.timeout', 15);
    }

    private function makePayment(float $amount): Payment
    {
        $payment = new Payment();
        $payment->ulid = 'ULID123';
        $payment->amount = $amount;
        $payment->transaction_reference = 'INV-123';
        $payment->provider_reference = null;

        return $payment;
    }
}

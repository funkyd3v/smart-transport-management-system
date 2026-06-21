<?php

declare(strict_types=1);

namespace App\Modules\Payment\Services;

use App\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Payment\Actions\RecordPaymentAction;
use App\Modules\Payment\Contracts\PaymentRepositoryInterface;
use App\Modules\Payment\DTOs\RecordPaymentDTO;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Events\PaymentCancelled;
use App\Modules\Payment\Events\PaymentFailed;
use App\Modules\Payment\Events\PaymentProcessing;
use App\Modules\Payment\Events\PaymentSucceeded;
use App\Modules\Payment\Events\PaymentValidated;
use App\Modules\Payment\Gateways\Bkash\BkashGateway;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Trip\Models\DueRecord;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ClientBkashPaymentService
{
    public function __construct(
        private readonly RecordPaymentAction $recordPaymentAction,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly BkashGateway $bkashGateway,
    ) {}

    public function getPendingDueRecords(User $user): Collection
    {
        $client = $this->resolveClient($user);

        return DueRecord::query()
            ->with(['trip:id,ulid,trip_code,pickup_point,delivery_point,load_date'])
            ->where('client_id', $client->id)
            ->where('is_settled', false)
            ->where('remaining_due', '>', 0)
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderByDesc('id')
            ->get();
    }

    public function initiate(User $user, string $tripUlid, float $amount, ?string $note = null): array
    {
        $client = $this->resolveClient($user);
        $dueRecord = $this->resolveDueRecord($client->id, $tripUlid);

        $remainingDue = (float) $dueRecord->remaining_due;

        if ($amount > $remainingDue) {
            throw ValidationException::withMessages([
                'amount' => 'Amount exceeds remaining due for this trip.',
            ]);
        }

        $paymentMethod = PaymentMethod::query()->firstOrCreate(
            ['name' => 'Mobile Banking'],
            ['description' => 'Payment via mobile banking services.'],
        );

        $transactionReference = $this->transactionReference($dueRecord->trip);

        $payment = ($this->recordPaymentAction)(new RecordPaymentDTO(
            payableType: Trip::class,
            payableId: (int) $dueRecord->trip_id,
            tripId: (int) $dueRecord->trip_id,
            clientId: (int) $client->id,
            paymentMethodId: (int) $paymentMethod->id,
            collectedBy: (int) $user->id,
            amount: $amount,
            transactionReference: $transactionReference,
            paymentDate: now()->toDateString(),
            isAdvance: false,
            note: $note,
            gateway: 'bkash',
            gatewayPayload: [
                'callback_url' => route('client.payments.bkash.callback'),
                'merchant_invoice_number' => $transactionReference,
                'payer_reference' => $this->payerReference($user),
            ],
        ));

        $latestAttempt = $payment->attempts()->latest('id')->first();
        $redirectUrl = (string) data_get($latestAttempt?->response_payload ?? [], 'bkashURL', '');

        if ($payment->status !== PaymentStatus::Initiated->value || $redirectUrl === '') {
            throw new RuntimeException('Unable to initialize bKash checkout at this moment.');
        }

        if (! $this->isTrustedBkashUrl($redirectUrl)) {
            throw new RuntimeException('Received an invalid payment redirect URL from bKash gateway.');
        }

        return [
            'payment' => $payment,
            'redirect_url' => $redirectUrl,
        ];
    }

    public function handleCallback(User $user, string $paymentId, ?string $status, array $payload): Payment
    {
        $client = $this->resolveClient($user);

        $payment = Payment::query()
            ->where('client_id', $client->id)
            ->where('gateway', 'bkash')
            ->where('provider_reference', $paymentId)
            ->latest('id')
            ->first();

        if ($payment === null) {
            throw ValidationException::withMessages([
                'payment' => 'Unable to locate this payment for your account.',
            ]);
        }

        if (in_array($payment->status, [
            PaymentStatus::Succeeded->value,
            PaymentStatus::Failed->value,
            PaymentStatus::Cancelled->value,
        ], true)) {
            return $payment;
        }

        $this->paymentRepository->createWebhook([
            'payment_id' => $payment->id,
            'gateway' => 'bkash',
            'event_type' => 'callback',
            'payload' => $payload,
            'signature' => null,
            'validated_at' => now(),
        ]);

        $normalizedStatus = Str::lower(trim((string) $status));

        if (in_array($normalizedStatus, ['cancel', 'cancelled', 'canceled'], true)) {
            return $this->finalizeWithoutExecution($payment, PaymentStatus::Cancelled, $user->id, $payload, 'Payment cancelled by user at bKash.');
        }

        if (in_array($normalizedStatus, ['failure', 'failed'], true)) {
            return $this->finalizeWithoutExecution($payment, PaymentStatus::Failed, $user->id, $payload, 'Payment failed at bKash checkout.');
        }

        return $this->executeAndValidate($payment, $paymentId, $user->id, $payload);
    }

    private function executeAndValidate(Payment $payment, string $paymentId, int $performedBy, array $payload): Payment
    {
        $events = [];

        $updatedPayment = DB::transaction(function () use (&$events, $payment, $paymentId, $performedBy, $payload): Payment {
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if (in_array($lockedPayment->status, [
                PaymentStatus::Succeeded->value,
                PaymentStatus::Failed->value,
                PaymentStatus::Cancelled->value,
            ], true)) {
                return $lockedPayment;
            }

            $attemptCount = (int) $lockedPayment->attempts()->count();

            $executeResult = $this->bkashGateway->executePayment($lockedPayment, $paymentId);

            $this->paymentRepository->createAttempt([
                'payment_id' => $lockedPayment->id,
                'gateway' => 'bkash',
                'attempt_no' => $attemptCount + 1,
                'status' => $executeResult->status,
                'request_payload' => [
                    'action' => 'execute',
                    'payment_id' => $paymentId,
                    'callback_payload' => $payload,
                ],
                'response_payload' => $executeResult->rawResponse,
                'attempted_at' => now(),
            ]);

            if (! $executeResult->success) {
                $failedPayment = $this->paymentRepository->updatePayment($lockedPayment, [
                    'status' => PaymentStatus::Failed->value,
                    'provider_reference' => $paymentId,
                ]);

                $this->paymentRepository->createTransaction([
                    'payment_id' => $failedPayment->id,
                    'gateway' => 'bkash',
                    'gateway_transaction_id' => $executeResult->transactionId,
                    'provider_reference' => $paymentId,
                    'status' => PaymentStatus::Failed->value,
                    'amount' => $failedPayment->amount,
                    'currency' => (string) config('payment.currency', 'BDT'),
                    'raw_response' => $executeResult->rawResponse,
                    'processed_at' => now(),
                ]);

                $this->paymentRepository->createAudit([
                    'payment_id' => $failedPayment->id,
                    'event' => 'payment_failed',
                    'description' => $executeResult->message,
                    'meta' => $executeResult->rawResponse,
                    'performed_by' => $performedBy,
                ]);

                $events[] = new PaymentFailed($failedPayment);

                return $failedPayment;
            }

            $validateResponse = $this->bkashGateway->validate($lockedPayment, ['payment_id' => $paymentId]);

            $this->paymentRepository->createAttempt([
                'payment_id' => $lockedPayment->id,
                'gateway' => 'bkash',
                'attempt_no' => $attemptCount + 2,
                'status' => $validateResponse->status ?? ($validateResponse->success ? PaymentStatus::Succeeded->value : PaymentStatus::Failed->value),
                'request_payload' => [
                    'action' => 'validate',
                    'payment_id' => $paymentId,
                ],
                'response_payload' => $validateResponse->rawResponse,
                'attempted_at' => now(),
            ]);

            $status = $this->resolveStatus($validateResponse->status, $validateResponse->success);

            $updatedPayment = $this->paymentRepository->updatePayment($lockedPayment, [
                'status' => $status->value,
                'provider_reference' => $validateResponse->providerReference ?? $paymentId,
                'gateway' => 'bkash',
            ]);

            $this->paymentRepository->createTransaction([
                'payment_id' => $updatedPayment->id,
                'gateway' => 'bkash',
                'gateway_transaction_id' => $validateResponse->gatewayTransactionId ?? $executeResult->transactionId,
                'provider_reference' => $validateResponse->providerReference ?? $paymentId,
                'status' => $status->value,
                'amount' => $updatedPayment->amount,
                'currency' => (string) config('payment.currency', 'BDT'),
                'raw_response' => [
                    'execute' => $executeResult->rawResponse,
                    'validate' => $validateResponse->rawResponse,
                ],
                'processed_at' => now(),
            ]);

            $this->paymentRepository->createAudit([
                'payment_id' => $updatedPayment->id,
                'event' => $status === PaymentStatus::Initiated ? 'payment_processing' : 'payment_'.$status->value,
                'description' => $validateResponse->message,
                'meta' => $validateResponse->rawResponse,
                'performed_by' => $performedBy,
            ]);

            if ($status === PaymentStatus::Succeeded) {
                $events[] = new PaymentValidated($updatedPayment);
                $events[] = new PaymentSucceeded($updatedPayment);
            } elseif ($status === PaymentStatus::Cancelled) {
                $events[] = new PaymentCancelled($updatedPayment);
            } elseif ($status === PaymentStatus::Initiated) {
                $events[] = new PaymentProcessing($updatedPayment);
            } else {
                $events[] = new PaymentFailed($updatedPayment);
            }

            return $updatedPayment;
        });

        foreach ($events as $event) {
            event($event);
        }

        return $updatedPayment;
    }

    private function finalizeWithoutExecution(
        Payment $payment,
        PaymentStatus $status,
        int $performedBy,
        array $payload,
        string $description,
    ): Payment {
        $events = [];

        $updatedPayment = DB::transaction(function () use (&$events, $payment, $status, $performedBy, $payload, $description): Payment {
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $attemptCount = (int) $lockedPayment->attempts()->count();

            $this->paymentRepository->createAttempt([
                'payment_id' => $lockedPayment->id,
                'gateway' => 'bkash',
                'attempt_no' => $attemptCount + 1,
                'status' => $status->value,
                'request_payload' => [
                    'action' => 'callback',
                    'provider_reference' => $lockedPayment->provider_reference,
                ],
                'response_payload' => $payload,
                'attempted_at' => now(),
            ]);

            $updatedPayment = $this->paymentRepository->updatePayment($lockedPayment, [
                'status' => $status->value,
                'provider_reference' => (string) ($lockedPayment->provider_reference ?? Arr::get($payload, 'paymentID')),
                'gateway' => 'bkash',
            ]);

            $this->paymentRepository->createTransaction([
                'payment_id' => $updatedPayment->id,
                'gateway' => 'bkash',
                'gateway_transaction_id' => null,
                'provider_reference' => $updatedPayment->provider_reference,
                'status' => $status->value,
                'amount' => $updatedPayment->amount,
                'currency' => (string) config('payment.currency', 'BDT'),
                'raw_response' => $payload,
                'processed_at' => now(),
            ]);

            $this->paymentRepository->createAudit([
                'payment_id' => $updatedPayment->id,
                'event' => 'payment_'.$status->value,
                'description' => $description,
                'meta' => $payload,
                'performed_by' => $performedBy,
            ]);

            if ($status === PaymentStatus::Cancelled) {
                $events[] = new PaymentCancelled($updatedPayment);
            } else {
                $events[] = new PaymentFailed($updatedPayment);
            }

            return $updatedPayment;
        });

        foreach ($events as $event) {
            event($event);
        }

        return $updatedPayment;
    }

    private function resolveClient(User $user): Client
    {
        $client = Client::query()->where('user_id', $user->id)->first();

        if ($client === null) {
            throw ValidationException::withMessages([
                'client' => 'No client profile is linked with your account.',
            ]);
        }

        return $client;
    }

    private function resolveDueRecord(int $clientId, string $tripUlid): DueRecord
    {
        $dueRecord = DueRecord::query()
            ->with('trip:id,ulid,trip_code')
            ->where('client_id', $clientId)
            ->where('is_settled', false)
            ->where('remaining_due', '>', 0)
            ->whereHas('trip', function ($query) use ($tripUlid): void {
                $query->where('ulid', $tripUlid);
            })
            ->first();

        if ($dueRecord === null) {
            throw ValidationException::withMessages([
                'trip_ulid' => 'Selected trip does not have any pending due for your account.',
            ]);
        }

        return $dueRecord;
    }

    private function resolveStatus(?string $status, bool $success): PaymentStatus
    {
        return match (Str::lower((string) $status)) {
            PaymentStatus::Cancelled->value => PaymentStatus::Cancelled,
            PaymentStatus::Initiated->value => PaymentStatus::Initiated,
            PaymentStatus::Failed->value => PaymentStatus::Failed,
            default => $success ? PaymentStatus::Succeeded : PaymentStatus::Failed,
        };
    }

    private function transactionReference(Trip $trip): string
    {
        return sprintf('BK-%s-%s', Str::upper((string) Str::substr($trip->ulid, 0, 8)), Str::upper(Str::random(8)));
    }

    private function payerReference(User $user): string
    {
        return Str::upper('CLIENT-'.$user->id);
    }

    private function isTrustedBkashUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = (string) parse_url($url, PHP_URL_HOST);

        if ($host === '') {
            return false;
        }

        if ($host === 'localhost' || $host === '127.0.0.1') {
            return true;
        }

        return Str::endsWith(Str::lower($host), ['bka.sh', 'bkash.com']);
    }
}

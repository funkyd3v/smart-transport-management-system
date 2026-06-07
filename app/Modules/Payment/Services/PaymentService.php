<?php

declare(strict_types=1);

namespace App\Modules\Payment\Services;

use App\Modules\Payment\Contracts\PaymentRepositoryInterface;
use App\Modules\Payment\DTOs\RecordPaymentDTO;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Events\PaymentCancelled;
use App\Modules\Payment\Events\PaymentFailed;
use App\Modules\Payment\Events\PaymentInitiated;
use App\Modules\Payment\Events\PaymentProcessing;
use App\Modules\Payment\Events\PaymentSucceeded;
use App\Modules\Payment\Events\PaymentValidated;
use App\Modules\Payment\Factories\PaymentGatewayFactory;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $repository,
        private readonly PaymentGatewayFactory $paymentGatewayFactory,
    ) {}

    public function record(RecordPaymentDTO $dto): Payment
    {
        $events = [];

        $payment = DB::transaction(function () use ($dto, &$events): Payment {
            $payment = $this->repository->createPayment([
                'ulid' => str()->ulid()->toBase32(),
                'trip_id' => $dto->tripId,
                'payable_type' => $dto->payableType,
                'payable_id' => $dto->payableId,
                'client_id' => $dto->clientId,
                'collected_by' => $dto->collectedBy,
                'payment_method_id' => $dto->paymentMethodId,
                'gateway' => $dto->gateway ?: config('payment.default_gateway', 'offline'),
                'status' => PaymentStatus::Initiated->value,
                'amount' => $dto->amount,
                'transaction_reference' => $dto->transactionReference,
                'provider_reference' => null,
                'payment_date' => $dto->paymentDate,
                'is_advance' => $dto->isAdvance,
                'note' => $dto->note,
            ]);

            $events[] = new PaymentInitiated($payment);

            $gateway = $this->paymentGatewayFactory->make($dto->gateway);
            $response = $gateway->initiate($payment, $dto->gatewayPayload);

            $attemptCount = (int) $payment->attempts()->count() + 1;

            $this->repository->createAttempt([
                'payment_id' => $payment->id,
                'gateway' => $gateway->key(),
                'attempt_no' => $attemptCount,
                'status' => $response->status ?? ($response->success ? PaymentStatus::Succeeded->value : PaymentStatus::Failed->value),
                'request_payload' => $dto->gatewayPayload,
                'response_payload' => $response->rawResponse,
                'attempted_at' => now(),
            ]);

            $this->repository->createTransaction([
                'payment_id' => $payment->id,
                'gateway' => $gateway->key(),
                'gateway_transaction_id' => $response->gatewayTransactionId,
                'provider_reference' => $response->providerReference,
                'status' => $response->status ?? ($response->success ? PaymentStatus::Succeeded->value : PaymentStatus::Failed->value),
                'amount' => $payment->amount,
                'currency' => (string) config('payment.currency', 'BDT'),
                'raw_response' => $response->rawResponse,
                'processed_at' => now(),
            ]);

            $status = match (strtolower((string) $response->status)) {
                PaymentStatus::Cancelled->value => PaymentStatus::Cancelled,
                PaymentStatus::Failed->value => PaymentStatus::Failed,
                PaymentStatus::Initiated->value => PaymentStatus::Initiated,
                default => $response->success ? PaymentStatus::Succeeded : PaymentStatus::Failed,
            };

            $payment = $this->repository->updatePayment($payment, [
                'status' => $status->value,
                'gateway' => $gateway->key(),
                'provider_reference' => $response->providerReference,
            ]);

            $this->repository->createAudit([
                'payment_id' => $payment->id,
                'event' => $status === PaymentStatus::Initiated ? 'payment_processing' : 'payment_'.$status->value,
                'description' => $response->message,
                'meta' => $response->rawResponse,
                'performed_by' => $dto->collectedBy,
            ]);

            if ($status === PaymentStatus::Succeeded) {
                $events[] = new PaymentValidated($payment);
                $events[] = new PaymentSucceeded($payment);
            } elseif ($status === PaymentStatus::Initiated) {
                $events[] = new PaymentProcessing($payment);
            } elseif ($status === PaymentStatus::Cancelled) {
                $events[] = new PaymentCancelled($payment);
            } else {
                $events[] = new PaymentFailed($payment);
            }

            return $payment;
        });

        foreach ($events as $event) {
            event($event);
        }

        return $payment;
    }
}

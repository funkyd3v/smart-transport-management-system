<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Payment\DTOs\RecordPaymentDTO;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;

class RecordPaymentAction
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function __invoke(RecordPaymentDTO $dto): Payment
    {
        return $this->paymentService->record($dto);
    }
}

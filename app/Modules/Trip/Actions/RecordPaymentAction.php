<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\RecordPaymentDTO;
use App\Modules\Trip\Models\Payment;
use App\Modules\Trip\Services\PaymentService;

class RecordPaymentAction
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function __invoke(RecordPaymentDTO $dto): Payment
    {
        return $this->paymentService->record($dto);
    }
}

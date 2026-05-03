<?php

namespace App\Modules\Payment\Services;

use App\Modules\Payment\Repositories\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(protected PaymentRepositoryInterface $repository) {}
}

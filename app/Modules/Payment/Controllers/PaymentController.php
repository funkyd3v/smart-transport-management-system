<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Requests\StorePaymentRequest;
use App\Modules\Payment\Requests\UpdatePaymentRequest;
use App\Modules\Payment\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}

    public function index() {}

    public function store(StorePaymentRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdatePaymentRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

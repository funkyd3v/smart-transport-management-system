<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invoice\Requests\StoreInvoiceRequest;
use App\Modules\Invoice\Requests\UpdateInvoiceRequest;
use App\Modules\Invoice\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $service) {}

    public function index() {}

    public function store(StoreInvoiceRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateInvoiceRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

<?php

namespace App\Modules\Cashbook\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cashbook\Requests\StoreCashbookRequest;
use App\Modules\Cashbook\Requests\UpdateCashbookRequest;
use App\Modules\Cashbook\Services\CashbookService;

class CashbookController extends Controller
{
    public function __construct(protected CashbookService $service) {}

    public function index() {}

    public function store(StoreCashbookRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateCashbookRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

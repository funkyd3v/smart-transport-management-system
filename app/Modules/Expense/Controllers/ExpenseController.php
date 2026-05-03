<?php

namespace App\Modules\Expense\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Expense\Requests\StoreExpenseRequest;
use App\Modules\Expense\Requests\UpdateExpenseRequest;
use App\Modules\Expense\Services\ExpenseService;

class ExpenseController extends Controller
{
    public function __construct(protected ExpenseService $service) {}

    public function index() {}

    public function store(StoreExpenseRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateExpenseRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

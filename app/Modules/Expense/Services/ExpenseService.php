<?php

namespace App\Modules\Expense\Services;

use App\Modules\Expense\Repositories\ExpenseRepositoryInterface;

class ExpenseService
{
    public function __construct(protected ExpenseRepositoryInterface $repository) {}
}

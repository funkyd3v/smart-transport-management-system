<?php

namespace App\Modules\Cashbook\Services;

use App\Modules\Cashbook\Repositories\CashbookRepositoryInterface;

class CashbookService
{
    public function __construct(protected CashbookRepositoryInterface $repository) {}
}

<?php

namespace App\Modules\Cashbook\Repositories;

use App\Modules\Cashbook\Models\DailyCashbook;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CashbookRepositoryInterface
{
    public function all(): Collection;

    public function findById(string $id): ?DailyCashbook;

    public function create(array $data): DailyCashbook;

    public function update(string $id, array $data): ?DailyCashbook;

    public function delete(string $id): bool;

    public function getLastBalance(): float;

    public function getDailySummary(Carbon $date): array;

    public function getMonthlySummary(Carbon $month): array;

    public function paginateEntries(array $filters, int $perPage = 15): LengthAwarePaginator;
}

<?php

namespace App\Modules\Report\Repositories;

interface ReportRepositoryInterface
{
    public function summary(): array;

    public function dailyProfitBreakdown(?string $date = null): array;

    public function monthlyProfitBreakdown(?int $year = null, ?int $month = null): array;

    public function all();

    public function findByUlid(string $ulid);

    public function create(array $data);

    public function update(string $ulid, array $data);

    public function delete(string $ulid);
}

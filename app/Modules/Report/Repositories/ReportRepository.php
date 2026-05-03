<?php

namespace App\Modules\Report\Repositories;

class ReportRepository implements ReportRepositoryInterface
{
    public function all() {}

    public function findByUlid(string $ulid) {}

    public function create(array $data) {}

    public function update(string $ulid, array $data) {}

    public function delete(string $ulid) {}
}

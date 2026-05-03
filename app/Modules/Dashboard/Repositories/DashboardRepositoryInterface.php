<?php

namespace App\Modules\Dashboard\Repositories;

interface DashboardRepositoryInterface
{
    public function all();

    public function findByUlid(string $ulid);

    public function create(array $data);

    public function update(string $ulid, array $data);

    public function delete(string $ulid);
}

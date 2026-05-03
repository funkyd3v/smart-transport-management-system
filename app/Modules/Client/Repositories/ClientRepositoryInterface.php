<?php

namespace App\Modules\Client\Repositories;

interface ClientRepositoryInterface
{
    public function all();

    public function findByUlid(string $ulid);

    public function create(array $data);

    public function update(string $ulid, array $data);

    public function delete(string $ulid);
}

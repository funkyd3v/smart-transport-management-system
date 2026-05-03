<?php

namespace App\Modules\Invoice\Repositories;

interface InvoiceRepositoryInterface
{
    public function all();

    public function findByUlid(string $ulid);

    public function create(array $data);

    public function update(string $ulid, array $data);

    public function delete(string $ulid);
}

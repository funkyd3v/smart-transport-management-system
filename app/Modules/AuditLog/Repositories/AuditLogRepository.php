<?php

namespace App\Modules\AuditLog\Repositories;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function all() {}

    public function findByUlid(string $ulid) {}

    public function create(array $data) {}

    public function update(string $ulid, array $data) {}

    public function delete(string $ulid) {}
}

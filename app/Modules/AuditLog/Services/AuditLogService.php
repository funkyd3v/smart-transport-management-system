<?php

namespace App\Modules\AuditLog\Services;

use App\Modules\AuditLog\Repositories\AuditLogRepositoryInterface;

class AuditLogService
{
    public function __construct(protected AuditLogRepositoryInterface $repository) {}
}

<?php

namespace App\Modules\AuditLog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AuditLog\Requests\StoreAuditLogRequest;
use App\Modules\AuditLog\Requests\UpdateAuditLogRequest;
use App\Modules\AuditLog\Services\AuditLogService;

class AuditLogController extends Controller
{
    public function __construct(protected AuditLogService $service) {}

    public function index() {}

    public function store(StoreAuditLogRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateAuditLogRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

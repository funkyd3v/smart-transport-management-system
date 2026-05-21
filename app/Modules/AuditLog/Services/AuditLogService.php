<?php

declare(strict_types=1);

namespace App\Modules\AuditLog\Services;

use App\Modules\AuditLog\Models\AuditLog;
use App\Modules\AuditLog\Repositories\AuditLogRepositoryInterface;
use Illuminate\Http\Request;

class AuditLogService
{
    public function __construct(protected AuditLogRepositoryInterface $repository) {}

    public function log(
        int|string|null $userId,
        string $action,
        ?string $tableName,
        int|string|null $recordId,
        ?array $oldValues,
        ?array $newValues,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): AuditLog {
        /** @var Request $request */
        $request = app(Request::class);

        return AuditLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress ?? $request->ip(),
            'user_agent' => $userAgent ?? $request->userAgent(),
        ]);
    }
}

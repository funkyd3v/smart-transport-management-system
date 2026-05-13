<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use Illuminate\View\View;

final class AuditLogController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.audit-logs.index', $this->service->auditLogsData());
    }
}

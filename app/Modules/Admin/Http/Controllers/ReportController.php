<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Admin\Services\AdminReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ReportController extends Controller
{
    public function __construct(
        private readonly AdminOperationsService $operationsService,
        private readonly AdminReportService $reportService,
    ) {}

    public function index(): View
    {
        return view('admin::pages.reports.index', $this->operationsService->reportsData($this->reportService->reportTypes()));
    }

    public function generate(Request $request): RedirectResponse
    {
        return back()->with('success', 'Report preview generated.');
    }

    public function download(string $type): RedirectResponse
    {
        return back()->with('success', 'Report download started: '.$type);
    }
}

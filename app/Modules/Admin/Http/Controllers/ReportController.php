<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Admin\Services\AdminReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class ReportController extends Controller
{
    public function __construct(
        private readonly AdminOperationsService $operationsService,
        private readonly AdminReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        $reportTypes = $this->reportService->reportTypes();
        $pageData = $this->operationsService->reportsData($reportTypes);

        $selectedType = (string) $request->input('report_type', '');
        $shouldPreview = (bool) $request->boolean('preview');

        if ($shouldPreview && $selectedType !== '' && $this->reportService->isValidReportType($selectedType)) {
            $pageData['selectedReportType'] = $selectedType;
            $pageData['previewReport'] = $this->reportService->previewPayload($selectedType, $pageData['stats']);
        } else {
            $pageData['selectedReportType'] = null;
            $pageData['previewReport'] = null;
        }

        return view('admin::pages.reports.index', $pageData);
    }

    public function generate(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'report_type' => ['required', 'string', Rule::in($this->reportService->reportTypes())],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $stats = $this->operationsService->reportsData($this->reportService->reportTypes())['stats'];

            return response()->json([
                'success' => true,
                'message' => 'Report preview generated.',
                'data' => [
                    'selected_report_type' => $validated['report_type'],
                    'preview_report' => $this->reportService->previewPayload($validated['report_type'], $stats),
                ],
            ]);
        }

        return redirect()->route('admin.reports.index', [
            'preview' => 1,
            'report_type' => $validated['report_type'],
        ])->with('toast_success', 'Report preview generated.');
    }

    public function download(string $type): Response
    {
        if (! $this->reportService->isValidReportType($type)) {
            abort(404);
        }

        $stats = $this->operationsService->reportsData($this->reportService->reportTypes())['stats'];
        $csv = $this->reportService->csvPayload($type, $stats);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-'.$type.'-'.now()->format('YmdHis').'.csv"',
        ]);
    }
}

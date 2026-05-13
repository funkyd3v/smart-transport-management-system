<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Due\Models\DueRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FinanceController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.finance.index', $this->service->financeOverviewData());
    }

    public function dues(): View
    {
        return view('admin::pages.finance.dues', $this->service->financeDuesData());
    }

    public function recordPayment(Request $request, DueRecord $due): RedirectResponse
    {
        return back()->with('success', 'Payment recorded successfully.');
    }

    public function cashbook(): View
    {
        return view('admin::pages.finance.cashbook', $this->service->financeCashbookData());
    }
}

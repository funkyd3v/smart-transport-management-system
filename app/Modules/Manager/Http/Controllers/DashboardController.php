<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Manager\Services\DashboardService;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $service) {}

    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        return view('manager::dashboard', [
            'totalTrucks' => $this->service->getTotalTrucks(),
            'runningTrips' => $this->service->getRunningTripsCount(),
            'workshopTrucks' => $this->service->getWorkshopTrucksCount(),
            'todayIncome' => $this->service->getTodayIncome(),
            'todayExpense' => $this->service->getTodayExpense(),
            'todayDue' => $this->service->getTodayDue(),
            'monthlyProfit' => $this->service->getMonthlyProfit(),
            'activeTrips' => $this->service->getActiveTrips(),
            'topDueClients' => $this->service->getTopDueClients(5),
            'totalOutstandingDue' => $this->service->getTotalOutstandingDue(),
            'monthlyFinancials' => $this->service->getMonthlyFinancials(6),
            'paymentMethodBreakdown' => $this->service->getPaymentMethodBreakdown(),
            'driverPerformance' => $this->service->getDriverPerformance(8),
            'spareInventorySummary' => $this->service->getSpareInventorySummary(),
            'totalSpareParts' => $this->service->getTotalSpareParts(),
            'recentCashbook' => $this->service->getRecentCashbook(7),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminDashboardService;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $service) {}

    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        return view('admin::dashboard', $this->service->dashboardData());
    }
}

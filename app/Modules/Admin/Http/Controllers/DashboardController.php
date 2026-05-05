<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Load admin dashboard metrics, approvals, and system-wide activity here.
        return view('admin::pages.dashboard.ecommerce');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Load manager dashboard KPIs, team assignments, and trip summaries here.
        return view('manager::dashboard');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Load driver assignments, delivery status, and expense snapshots here.
        return view('driver::dashboard');
    }
}

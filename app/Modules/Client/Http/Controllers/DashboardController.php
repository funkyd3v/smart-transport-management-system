<?php

declare(strict_types=1);

namespace App\Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Load client-specific account summaries or announcements here if needed later.
        return view('client::dashboard');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Services\ClientDashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private ClientDashboardService $service) {}

    public function __invoke(): View
    {
        /** @var \App\Modules\Auth\Models\User $user */
        $user   = Auth::user();
        $client = Client::where('user_id', $user->id)->firstOrFail();

        $data = $this->service->getDashboardData($client);

        return view('client::dashboard', array_merge(['client' => $client], $data));
    }
}

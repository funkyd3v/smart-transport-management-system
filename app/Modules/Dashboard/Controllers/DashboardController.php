<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Requests\StoreDashboardRequest;
use App\Modules\Dashboard\Requests\UpdateDashboardRequest;
use App\Modules\Dashboard\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $service) {}

    public function index() {}

    public function store(StoreDashboardRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateDashboardRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Report\Requests\StoreReportRequest;
use App\Modules\Report\Requests\UpdateReportRequest;
use App\Modules\Report\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(protected ReportService $service) {}

    public function index() {}

    public function store(StoreReportRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateReportRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

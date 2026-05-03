<?php

namespace App\Modules\Due\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Due\Requests\StoreDueRequest;
use App\Modules\Due\Requests\UpdateDueRequest;
use App\Modules\Due\Services\DueService;

class DueController extends Controller
{
    public function __construct(protected DueService $service) {}

    public function index() {}

    public function store(StoreDueRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateDueRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

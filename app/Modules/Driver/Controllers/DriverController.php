<?php

namespace App\Modules\Driver\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Driver\Requests\StoreDriverRequest;
use App\Modules\Driver\Requests\UpdateDriverRequest;
use App\Modules\Driver\Services\DriverService;

class DriverController extends Controller
{
    public function __construct(protected DriverService $service) {}

    public function index() {}

    public function store(StoreDriverRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateDriverRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

<?php

namespace App\Modules\Truck\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Truck\Requests\StoreTruckRequest;
use App\Modules\Truck\Requests\UpdateTruckRequest;
use App\Modules\Truck\Services\TruckService;

class TruckController extends Controller
{
    public function __construct(protected TruckService $service) {}

    public function index() {}

    public function store(StoreTruckRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateTruckRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

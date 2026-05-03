<?php

namespace App\Modules\Trip\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Trip\Requests\StoreTripRequest;
use App\Modules\Trip\Requests\UpdateTripRequest;
use App\Modules\Trip\Services\TripService;

class TripController extends Controller
{
    public function __construct(protected TripService $service) {}

    public function index() {}

    public function store(StoreTripRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateTripRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

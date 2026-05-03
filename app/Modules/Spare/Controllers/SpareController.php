<?php

namespace App\Modules\Spare\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Spare\Requests\StoreSpareRequest;
use App\Modules\Spare\Requests\UpdateSpareRequest;
use App\Modules\Spare\Services\SpareService;

class SpareController extends Controller
{
    public function __construct(protected SpareService $service) {}

    public function index() {}

    public function store(StoreSpareRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateSpareRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

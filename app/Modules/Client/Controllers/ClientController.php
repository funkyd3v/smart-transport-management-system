<?php

namespace App\Modules\Client\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Client\Requests\StoreClientRequest;
use App\Modules\Client\Requests\UpdateClientRequest;
use App\Modules\Client\Services\ClientService;

class ClientController extends Controller
{
    public function __construct(protected ClientService $service) {}

    public function index() {}

    public function store(StoreClientRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateClientRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

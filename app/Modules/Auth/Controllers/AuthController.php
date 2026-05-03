<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\StoreAuthRequest;
use App\Modules\Auth\Requests\UpdateAuthRequest;
use App\Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $service) {}

    public function index() {}

    public function store(StoreAuthRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateAuthRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}

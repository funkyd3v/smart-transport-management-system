<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\AuthRepositoryInterface;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $repository) {}
}

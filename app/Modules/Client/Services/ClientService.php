<?php

namespace App\Modules\Client\Services;

use App\Modules\Client\Repositories\ClientRepositoryInterface;

class ClientService
{
    public function __construct(protected ClientRepositoryInterface $repository) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Spare\Actions;

use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Services\SpareService;

class DeleteSparePartAction
{
    public function __construct(private readonly SpareService $service) {}

    public function __invoke(SparePart $part): bool
    {
        return $this->service->deletePart($part);
    }
}

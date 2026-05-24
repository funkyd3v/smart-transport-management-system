<?php

declare(strict_types=1);

namespace App\Modules\Spare\Actions;

use App\Modules\Spare\DTOs\UpdateSparePartDTO;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Services\SpareService;

class UpdateSparePartAction
{
    public function __construct(private readonly SpareService $service) {}

    public function __invoke(SparePart $part, UpdateSparePartDTO $dto): SparePart
    {
        return $this->service->updatePart($part, $dto);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Spare\Actions;

use App\Modules\Spare\DTOs\CreateSparePartDTO;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Services\SpareService;

class CreateSparePartAction
{
    public function __construct(private readonly SpareService $service) {}

    public function __invoke(CreateSparePartDTO $dto): SparePart
    {
        return $this->service->createPart($dto);
    }
}

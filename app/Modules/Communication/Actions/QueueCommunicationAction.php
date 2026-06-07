<?php

declare(strict_types=1);

namespace App\Modules\Communication\Actions;

use App\Modules\Communication\DTOs\CommunicationRequestDTO;
use App\Modules\Communication\Models\Communication;
use App\Modules\Communication\Services\CommunicationService;

class QueueCommunicationAction
{
    public function __construct(private readonly CommunicationService $communicationService) {}

    public function __invoke(CommunicationRequestDTO $dto): Communication
    {
        return $this->communicationService->queue($dto);
    }
}

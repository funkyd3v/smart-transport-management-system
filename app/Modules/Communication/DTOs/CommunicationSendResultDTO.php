<?php

declare(strict_types=1);

namespace App\Modules\Communication\DTOs;

use App\Modules\Communication\Models\Communication;

readonly class CommunicationSendResultDTO
{
    public function __construct(
        public Communication $communication,
        public bool $success,
    ) {}
}

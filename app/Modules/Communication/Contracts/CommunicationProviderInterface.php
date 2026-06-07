<?php

declare(strict_types=1);

namespace App\Modules\Communication\Contracts;

use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Models\Communication;

interface CommunicationProviderInterface
{
    public function key(): string;

    public function send(Communication $communication): CommunicationDispatchResultDTO;
}

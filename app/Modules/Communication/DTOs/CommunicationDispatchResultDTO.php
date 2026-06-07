<?php

declare(strict_types=1);

namespace App\Modules\Communication\DTOs;

readonly class CommunicationDispatchResultDTO
{
    public function __construct(
        public bool $success,
        public ?string $providerMessageId,
        public ?string $status,
        public ?string $message,
        public array $rawResponse = [],
    ) {}
}

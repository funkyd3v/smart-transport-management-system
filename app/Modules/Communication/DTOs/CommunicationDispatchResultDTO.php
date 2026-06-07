<?php

declare(strict_types=1);

namespace App\Modules\Communication\DTOs;

readonly class CommunicationDispatchResultDTO
{
    public function __construct(
        public bool $success,
        public ?string $provider,
        public ?string $providerMessageId,
        public ?string $status,
        public ?string $responseCode,
        public ?string $message,
        public array $rawResponse = [],
    ) {}
}

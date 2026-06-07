<?php

declare(strict_types=1);

namespace App\Modules\Communication\DTOs;

use App\Modules\Communication\Enums\CommunicationChannel;

readonly class CommunicationRequestDTO
{
    public function __construct(
        public CommunicationChannel $channel,
        public string $recipient,
        public string $subject,
        public string $body,
        public ?string $provider,
        public ?string $templateKey,
        public array $templateData,
        public ?int $requestedBy,
        public ?string $referenceType,
        public ?string $referenceId,
        public ?string $scheduledAt,
        public array $metadata = [],
    ) {}
}

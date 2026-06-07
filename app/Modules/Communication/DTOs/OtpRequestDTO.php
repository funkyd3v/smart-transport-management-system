<?php

declare(strict_types=1);

namespace App\Modules\Communication\DTOs;

use App\Modules\Communication\Enums\OtpPurpose;

readonly class OtpRequestDTO
{
    public function __construct(
        public OtpPurpose $purpose,
        public string $recipient,
        public int $expiresInMinutes,
        public ?int $requestedBy,
        public ?string $referenceType,
        public ?string $referenceId,
    ) {}
}

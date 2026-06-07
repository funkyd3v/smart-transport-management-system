<?php

declare(strict_types=1);

namespace App\Modules\Communication\DTOs;

use App\Modules\Communication\Enums\OtpPurpose;

readonly class OtpVerificationDTO
{
    public function __construct(
        public OtpPurpose $purpose,
        public string $recipient,
        public string $code,
    ) {}
}

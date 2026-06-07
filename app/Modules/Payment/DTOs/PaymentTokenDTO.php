<?php

declare(strict_types=1);

namespace App\Modules\Payment\DTOs;

readonly class PaymentTokenDTO
{
    public function __construct(
        public bool $success,
        public ?string $tokenType,
        public ?string $accessToken,
        public ?string $refreshToken,
        public ?int $expiresIn,
        public string $message,
        public array $rawResponse = [],
    ) {}
}

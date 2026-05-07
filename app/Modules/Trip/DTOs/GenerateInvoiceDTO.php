<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use App\Modules\Trip\Http\Requests\GenerateInvoiceRequest;

readonly class GenerateInvoiceDTO
{
    public function __construct(
        public string $tripUlid,
        public int $issuedBy,
        public ?string $companyLogoPath,
        public ?string $authoritySignaturePath,
    ) {}

    public static function fromRequest(GenerateInvoiceRequest $request): self
    {
        $data = $request->validated();

        return new self(
            tripUlid: (string) $data['trip_ulid'],
            issuedBy: (int) $request->user()->id,
            companyLogoPath: $data['company_logo_path'] ?? null,
            authoritySignaturePath: $data['authority_signature_path'] ?? null,
        );
    }
}

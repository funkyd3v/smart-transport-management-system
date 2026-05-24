<?php

declare(strict_types=1);

namespace App\Modules\Admin\DTOs\Settings;

use App\Modules\Admin\Http\Requests\Settings\UpdateGeneralSettingRequest;
use Illuminate\Http\UploadedFile;

final readonly class GeneralSettingDTO
{
    public function __construct(
        public string $companyName,
        public string $companyAddress,
        public string $contactNumber,
        public string $emailAddress,
        public string $currencySymbol,
        public string $timezone,
        public string $dateFormat,
        public ?UploadedFile $companyLogo,
    ) {}

    public static function fromRequest(UpdateGeneralSettingRequest $request): self
    {
        return new self(
            companyName: (string) $request->validated('company_name'),
            companyAddress: (string) $request->validated('company_address'),
            contactNumber: (string) $request->validated('contact_number'),
            emailAddress: (string) $request->validated('email_address'),
            currencySymbol: (string) $request->validated('currency_symbol'),
            timezone: (string) $request->validated('timezone'),
            dateFormat: (string) $request->validated('date_format'),
            companyLogo: $request->file('company_logo'),
        );
    }
}

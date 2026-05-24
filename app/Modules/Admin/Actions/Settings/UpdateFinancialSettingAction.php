<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions\Settings;

use App\Modules\Admin\DTOs\Settings\FinancialSettingDTO;
use App\Modules\Admin\Services\Settings\FinancialSettingService;

final class UpdateFinancialSettingAction
{
    public function __construct(private readonly FinancialSettingService $service) {}

    public function __invoke(FinancialSettingDTO $dto): void
    {
        $this->service->update($dto);
    }
}

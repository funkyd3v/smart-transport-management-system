<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions\Settings;

use App\Modules\Admin\DTOs\Settings\GeneralSettingDTO;
use App\Modules\Admin\Services\Settings\GeneralSettingService;

final class UpdateGeneralSettingAction
{
    public function __construct(private readonly GeneralSettingService $service) {}

    public function __invoke(GeneralSettingDTO $dto): void
    {
        $this->service->update($dto);
    }
}

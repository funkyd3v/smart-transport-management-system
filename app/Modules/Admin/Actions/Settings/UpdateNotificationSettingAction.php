<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions\Settings;

use App\Modules\Admin\DTOs\Settings\NotificationSettingDTO;
use App\Modules\Admin\Services\Settings\NotificationSettingService;

final class UpdateNotificationSettingAction
{
    public function __construct(private readonly NotificationSettingService $service) {}

    public function __invoke(NotificationSettingDTO $dto): void
    {
        $this->service->update($dto);
    }
}

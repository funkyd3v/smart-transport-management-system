<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Actions\Settings\UpdateNotificationSettingAction;
use App\Modules\Admin\DTOs\Settings\NotificationSettingDTO;
use App\Modules\Admin\Http\Requests\Settings\UpdateNotificationSettingRequest;
use App\Modules\Admin\Services\Settings\NotificationSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class NotificationSettingController extends Controller
{
    public function __construct(
        private readonly NotificationSettingService $service,
        private readonly UpdateNotificationSettingAction $action,
    ) {}

    public function index(): View
    {
        return view('admin::pages.settings.notifications.index', [
            'settings' => $this->service->settings(),
        ]);
    }

    public function update(UpdateNotificationSettingRequest $request): RedirectResponse
    {
        ($this->action)(NotificationSettingDTO::fromRequest($request));

        return redirect()->route('admin.settings.notifications.index')
            ->with('toast_success', 'Notification settings updated successfully.');
    }
}

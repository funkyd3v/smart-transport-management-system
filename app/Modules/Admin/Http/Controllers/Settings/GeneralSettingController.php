<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Actions\Settings\UpdateGeneralSettingAction;
use App\Modules\Admin\DTOs\Settings\GeneralSettingDTO;
use App\Modules\Admin\Http\Requests\Settings\UpdateGeneralSettingRequest;
use App\Modules\Admin\Services\Settings\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class GeneralSettingController extends Controller
{
    public function __construct(
        private readonly GeneralSettingService $service,
        private readonly UpdateGeneralSettingAction $action,
    ) {}

    public function index(): View
    {
        return view('admin::pages.settings.general.index', [
            'settings' => $this->service->settings(),
        ]);
    }

    public function update(UpdateGeneralSettingRequest $request): RedirectResponse
    {
        ($this->action)(GeneralSettingDTO::fromRequest($request));

        return redirect()
            ->route('admin.settings.general.index')
            ->with('toast_success', 'General settings updated successfully.');
    }
}

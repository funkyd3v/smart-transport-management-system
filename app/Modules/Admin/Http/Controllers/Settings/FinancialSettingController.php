<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Actions\Settings\UpdateFinancialSettingAction;
use App\Modules\Admin\DTOs\Settings\FinancialSettingDTO;
use App\Modules\Admin\Http\Requests\Settings\UpdateFinancialSettingRequest;
use App\Modules\Admin\Services\Settings\FinancialSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class FinancialSettingController extends Controller
{
    public function __construct(
        private readonly FinancialSettingService $service,
        private readonly UpdateFinancialSettingAction $action,
    ) {}

    public function index(): View
    {
        return view('admin::pages.settings.financial.index', [
            'settings' => $this->service->settings(),
        ]);
    }

    public function update(UpdateFinancialSettingRequest $request): RedirectResponse
    {
        ($this->action)(FinancialSettingDTO::fromRequest($request));

        return redirect()
            ->route('admin.settings.financial.index')
            ->with('toast_success', 'Financial settings updated successfully.');
    }
}

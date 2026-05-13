<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SettingsController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.settings.index', $this->service->settingsData());
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        return back()->with('success', ucfirst($section).' settings updated successfully.');
    }
}

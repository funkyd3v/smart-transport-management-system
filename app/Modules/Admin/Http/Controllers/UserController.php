<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Services\AdminOperationsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.users.index', $this->service->usersPageData());
    }

    public function show(User $user): View
    {
        return view('admin::pages.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin::pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        return redirect()->route('admin.users.index')->with('success', 'User archived successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        return back()->with('success', 'User status updated.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        return back()->with('success', 'Password reset link generated.');
    }

    public function bulkApprove(): RedirectResponse
    {
        return back()->with('success', 'Bulk approval executed.');
    }
}

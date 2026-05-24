<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Actions\Settings\CreateUserAction;
use App\Modules\Admin\Actions\Settings\ToggleUserStatusAction;
use App\Modules\Admin\Actions\Settings\UpdateUserAction;
use App\Modules\Admin\DTOs\Settings\UserDTO;
use App\Modules\Admin\Http\Requests\Settings\StoreUserRequest;
use App\Modules\Admin\Http\Requests\Settings\UpdateUserRequest;
use App\Modules\Admin\Services\Settings\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

final class UserManagementController extends Controller
{
    public function __construct(
        private readonly UserManagementService $service,
        private readonly CreateUserAction $createAction,
        private readonly UpdateUserAction $updateAction,
        private readonly ToggleUserStatusAction $toggleAction,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'role', 'status']);

        return view('admin::pages.settings.users.index', [
            ...$this->service->indexData($filters),
            'currentUserId' => (int) auth()->id(),
        ]);
    }

    public function create(): View
    {
        return view('admin::pages.settings.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        ($this->createAction)(UserDTO::fromStoreRequest($request));

        return redirect()->route('admin.settings.users.index')
            ->with('toast_success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin::pages.settings.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            ($this->updateAction)($user, UserDTO::fromUpdateRequest($request), (int) auth()->id());

            return redirect()->route('admin.settings.users.index')
                ->with('toast_success', 'User updated successfully.');
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('toast_error', $exception->getMessage());
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('toast_error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.settings.users.index')
            ->with('toast_success', 'User archived successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            ($this->toggleAction)($user, (int) auth()->id());

            return back()->with('toast_success', 'User status updated successfully.');
        } catch (RuntimeException $exception) {
            return back()->with('toast_error', $exception->getMessage());
        }
    }
}

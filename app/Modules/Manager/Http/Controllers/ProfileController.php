<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Manager\Http\Requests\Profile\UpdateManagerProfileRequest;
use App\Modules\Manager\Services\ProfileService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

final class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService) {}

    /**
     * @throws AuthorizationException
     */
    public function index(): View
    {
        /** @var User $manager */
        $manager = request()->user();

        return view('manager::pages.profile', [
            'manager' => $manager,
            'stats' => $this->profileService->managerStats($manager),
            'completionRate' => $this->profileService->profileCompletion($manager),
            'recentTrips' => $this->profileService->recentTrips($manager),
        ]);
    }

    public function update(UpdateManagerProfileRequest $request): JsonResponse|RedirectResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $validated = $request->validated();

        $manager->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        if ($manager->isDirty('email')) {
            $manager->email_verified_at = null;
        }

        $manager->save();

        if ($manager instanceof MustVerifyEmail && $manager->wasChanged('email')) {
            $manager->sendEmailVerificationNotification();
        }

        if ($request->hasFile('avatar')) {
            $manager
                ->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');

            // Refresh to ensure accessor resolves the latest media item.
            $manager->refresh();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully.',
                'user' => [
                    'name' => (string) $manager->name,
                    'email' => (string) $manager->email,
                    'phone' => (string) $manager->phone,
                    'avatar_url' => (string) $manager->avatar_url,
                ],
            ]);
        }

        return redirect()
            ->route('manager.profile')
            ->with('success', 'Profile updated successfully.');
    }
}

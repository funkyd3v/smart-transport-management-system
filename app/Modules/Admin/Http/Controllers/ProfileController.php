<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Http\Requests\UpdateAvatarRequest;
use App\Modules\Admin\Http\Requests\UpdateCompanyRequest;
use App\Modules\Admin\Http\Requests\UpdatePasswordRequest;
use App\Modules\Admin\Http\Requests\UpdatePersonalInfoRequest;
use App\Modules\Admin\Repositories\ProfileRepositoryInterface;
use App\Modules\Admin\Services\ProfileService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly ProfileRepositoryInterface $profileRepository,
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureAdmin($user);

        $user->load(['companySetting', 'notificationPreferences']);

        return view('admin::pages.profile', [
            'user' => $user,
            'notificationPreferences' => $this->profileRepository->getNotificationPreferences((string) $user->id),
            'loginHistory' => $this->profileRepository->getLoginHistory((string) $user->id, 10),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updatePersonal(UpdatePersonalInfoRequest $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $result = $this->profileService->updatePersonalInfo($request->user(), $request->validated());

        return $this->jsonResult($result, $result['success'] ? 200 : 500, [
            'user' => $result['user'] ?? null,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $result = $this->profileService->updatePassword(
            $request->user(),
            (string) $request->validated('current_password'),
            (string) $request->validated('password')
        );

        $status = 200;
        if (! $result['success']) {
            $status = ($result['field'] ?? null) === 'current_password' ? 422 : 500;
        }

        return $this->jsonResult($result, $status);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $result = $this->profileService->uploadAvatar($request->user(), $request->file('avatar'));

        return $this->jsonResult($result, $result['success'] ? 200 : 500, [
            'avatar_url' => $result['avatar_url'] ?? null,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateCompany(UpdateCompanyRequest $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $result = $this->profileService->updateCompany($request->user(), $request->validated());

        return $this->jsonResult($result, $result['success'] ? 200 : 500, [
            'company' => $result['company'] ?? null,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateCompanyLogo(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $validated = $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
        ]);

        $result = $this->profileService->uploadCompanyLogo($request->user(), $validated['logo']);

        return $this->jsonResult($result, $result['success'] ? 200 : 500, [
            'logo_url' => $result['logo_url'] ?? null,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateCompanySignature(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $validated = $request->validate([
            'signature' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $result = $this->profileService->uploadCompanySignature($request->user(), $validated['signature']);

        return $this->jsonResult($result, $result['success'] ? 200 : 500, [
            'signature_url' => $result['signature_url'] ?? null,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateNotification(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $validated = $request->validate([
            'event' => ['required', 'string'],
            'channel' => ['required', 'in:in_app,email,sms'],
            'enabled' => ['required', 'boolean'],
        ]);

        $result = $this->profileService->updateNotificationPreference(
            (string) $request->user()->id,
            (string) $validated['event'],
            (string) $validated['channel'],
            (bool) $validated['enabled']
        );

        return $this->jsonResult($result, $result['success'] ? 200 : 422);
    }

    /**
     * @throws AuthorizationException
     */
    public function stats(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Profile stats fetched',
            'data' => $this->profileRepository->getProfileStats((string) $request->user()->id),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function sessions(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Sessions fetched',
            'data' => [
                'current_session_id' => $request->session()->getId(),
                'sessions' => $this->profileRepository->getActiveSessions((string) $request->user()->id),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroySessions(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $result = $this->profileService->terminateOtherSessions($request->user(), (string) $validated['password']);

        return $this->jsonResult($result, $result['success'] ? 200 : 422);
    }

    /**
     * @throws AuthorizationException
     */
    public function activityLog(Request $request): JsonResponse
    {
        $this->ensureAdmin($request->user());

        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'type' => ['nullable', 'in:all,trip,payment,profile,auth,other'],
            'page' => ['nullable', 'integer'],
        ]);

        $logs = $this->profileRepository->getActivityLog((string) $request->user()->id, $filters);

        return response()->json([
            'success' => true,
            'message' => 'Activity log fetched',
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function exportActivity(Request $request): Response
    {
        $this->ensureAdmin($request->user());

        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'type' => ['nullable', 'in:all,trip,payment,profile,auth,other'],
        ]);

        $csv = $this->profileService->exportActivityLog((string) $request->user()->id, $filters);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity-log-'.now()->format('YmdHis').'.csv"',
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    private function ensureAdmin(?User $user): void
    {
        if ($user === null || ! $user->hasRole('admin')) {
            throw new AuthorizationException('Unauthorized action.');
        }
    }

    private function jsonResult(array $result, int $status, array $data = []): JsonResponse
    {
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Success',
                'data' => array_filter($data, static fn ($value): bool => $value !== null),
            ], $status);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed',
            'errors' => array_filter([
                'field' => $result['field'] ?? null,
            ]),
        ], $status);
    }
}

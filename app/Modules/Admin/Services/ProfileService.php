<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services;

use App\Models\User;
use App\Modules\Admin\Models\NotificationPreference;
use App\Modules\Admin\Repositories\ProfileRepositoryInterface;
use App\Modules\AuditLog\Services\AuditLogService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class ProfileService
{
    public function __construct(
        private readonly ProfileRepositoryInterface $profileRepository,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function updatePersonalInfo(User $user, array $data): array
    {
        try {
            $original = Arr::only($user->toArray(), array_keys($data));
            $updatedUser = $this->profileRepository->updatePersonalInfo($user, $data);
            $changes = array_diff_assoc(Arr::only($updatedUser->toArray(), array_keys($data)), $original);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.personal_updated',
                tableName: 'users',
                recordId: $user->id,
                oldValues: $original,
                newValues: $changes,
            );

            return [
                'success' => true,
                'user' => $updatedUser,
                'message' => 'Profile updated successfully',
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to update personal info', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to update profile information right now.',
            ];
        }
    }

    public function updatePassword(User $user, string $currentPassword, string $newPassword): array
    {
        try {
            if (! Hash::check($currentPassword, (string) $user->password)) {
                return [
                    'success' => false,
                    'field' => 'current_password',
                    'message' => 'Current password is incorrect',
                ];
            }

            $user->forceFill([
                'password' => $newPassword,
            ])->save();

            Auth::logoutOtherDevices($newPassword);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.password_changed',
                tableName: 'users',
                recordId: $user->id,
                oldValues: null,
                newValues: ['password_changed_at' => now()->toIso8601String()],
            );

            return [
                'success' => true,
                'message' => 'Password changed successfully',
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to change password', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to change password right now.',
            ];
        }
    }

    public function uploadAvatar(User $user, UploadedFile $file): array
    {
        try {
            if (filled($user->avatar)) {
                Storage::disk('public')->delete((string) $user->avatar);
            }

            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            $updatedUser = $this->profileRepository->updateAvatar($user, $path);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.avatar_updated',
                tableName: 'users',
                recordId: $user->id,
                oldValues: ['avatar' => $user->avatar],
                newValues: ['avatar' => $path],
            );

            return [
                'success' => true,
                'avatar_url' => $updatedUser->avatar_url,
                'message' => 'Avatar updated',
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to upload avatar', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to upload avatar right now.',
            ];
        }
    }

    public function updateCompany(User $user, array $data): array
    {
        try {
            $company = $this->profileRepository->updateCompany($user, $data);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.company_updated',
                tableName: 'company_settings',
                recordId: $company->id,
                oldValues: null,
                newValues: $data,
            );

            return [
                'success' => true,
                'message' => 'Company information updated',
                'company' => $company,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to update company info', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to update company information right now.',
            ];
        }
    }

    public function uploadCompanyLogo(User $user, UploadedFile $file): array
    {
        try {
            $company = $user->companySetting;
            if ($company !== null && filled($company->logo_path)) {
                Storage::disk('public')->delete((string) $company->logo_path);
            }

            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('company/logos', $filename, 'public');

            $company = $this->profileRepository->updateCompanyLogo($user, $path);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.company_logo_updated',
                tableName: 'company_settings',
                recordId: $company->id,
                oldValues: null,
                newValues: ['logo_path' => $path],
            );

            return [
                'success' => true,
                'logo_url' => $company->logo_url,
                'message' => 'Company logo updated',
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to upload company logo', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to upload company logo right now.',
            ];
        }
    }

    public function uploadCompanySignature(User $user, UploadedFile $file): array
    {
        try {
            $company = $user->companySetting;
            if ($company !== null && filled($company->signature_path)) {
                Storage::disk('public')->delete((string) $company->signature_path);
            }

            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('company/signatures', $filename, 'public');

            $company = $this->profileRepository->updateCompanySignature($user, $path);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.company_signature_updated',
                tableName: 'company_settings',
                recordId: $company->id,
                oldValues: null,
                newValues: ['signature_path' => $path],
            );

            return [
                'success' => true,
                'signature_url' => $company->signature_url,
                'message' => 'Company signature updated',
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to upload company signature', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to upload company signature right now.',
            ];
        }
    }

    public function updateNotificationPreference(string $userId, string $event, string $channel, bool $enabled): array
    {
        try {
            if (! in_array($event, NotificationPreference::EVENTS, true)) {
                return ['success' => false, 'message' => 'Invalid event provided'];
            }

            if (! in_array($channel, NotificationPreference::CHANNELS, true)) {
                return ['success' => false, 'message' => 'Invalid channel provided'];
            }

            $this->profileRepository->updateNotificationPreference($userId, $event, $channel, $enabled);

            return ['success' => true, 'message' => 'Preference saved'];
        } catch (Throwable $exception) {
            Log::error('Failed to update notification preference', [
                'user_id' => $userId,
                'event' => $event,
                'channel' => $channel,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update notification preference',
            ];
        }
    }

    public function terminateOtherSessions(User $user, string $password): array
    {
        try {
            if (! Hash::check($password, (string) $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Incorrect password',
                ];
            }

            Auth::logoutOtherDevices($password);

            $this->auditLogService->log(
                userId: $user->id,
                action: 'profile.sessions_terminated',
                tableName: 'sessions',
                recordId: null,
                oldValues: null,
                newValues: ['terminated_at' => now()->toIso8601String()],
            );

            return [
                'success' => true,
                'message' => 'All other sessions have been terminated',
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to terminate sessions', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to terminate sessions right now.',
            ];
        }
    }

    public function exportActivityLog(string $userId, array $filters): string
    {
        $rows = $this->profileRepository->getActivityLogCollection($userId, $filters);

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Date', 'Action', 'Description', 'IP Address', 'Status']);

        foreach ($rows as $row) {
            $status = str_contains((string) $row->action, 'failed') ? 'Failed' : 'Success';
            $description = (string) ($row->new_values['description'] ?? $row->table_name ?? '-');

            fputcsv($csv, [
                optional($row->created_at)->format('Y-m-d H:i:s'),
                (string) $row->action,
                $description,
                (string) ($row->ip_address ?? '-'),
                $status,
            ]);
        }

        rewind($csv);
        $content = (string) stream_get_contents($csv);
        fclose($csv);

        return $content;
    }
}

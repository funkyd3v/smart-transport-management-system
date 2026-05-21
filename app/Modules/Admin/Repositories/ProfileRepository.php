<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use App\Models\User;
use App\Modules\Admin\Models\CompanySetting;
use App\Modules\Admin\Models\LoginHistory;
use App\Modules\Admin\Models\NotificationPreference;
use App\Modules\AuditLog\Models\AuditLog;
use App\Modules\Payment\Models\Payment;
use App\Modules\Trip\Models\Invoice;
use App\Modules\Trip\Models\Trip;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ProfileRepository implements ProfileRepositoryInterface
{
    public function updatePersonalInfo(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }

    public function updateAvatar(User $user, string $path): User
    {
        $user->forceFill(['avatar' => $path])->save();

        return $user->refresh();
    }

    public function updateCompany(User $user, array $data): CompanySetting
    {
        return CompanySetting::query()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }

    public function updateCompanyLogo(User $user, string $path): CompanySetting
    {
        return CompanySetting::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['logo_path' => $path]
        );
    }

    public function updateCompanySignature(User $user, string $path): CompanySetting
    {
        return CompanySetting::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['signature_path' => $path]
        );
    }

    public function getNotificationPreferences(string $userId): Collection
    {
        return NotificationPreference::getForUser($userId);
    }

    public function updateNotificationPreference(string $userId, string $event, string $channel, bool $enabled): NotificationPreference
    {
        return NotificationPreference::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'event' => $event,
                'channel' => $channel,
            ],
            ['enabled' => $enabled]
        );
    }

    public function getLoginHistory(string $userId, int $limit = 10): Collection
    {
        return LoginHistory::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getActiveSessions(string $userId): Collection
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function (object $session): array {
                $lastActivity = Carbon::createFromTimestamp((int) $session->last_activity);

                return [
                    'id' => (string) $session->id,
                    'ip_address' => (string) ($session->ip_address ?? ''),
                    'user_agent' => (string) ($session->user_agent ?? ''),
                    'last_activity' => $lastActivity->toIso8601String(),
                    'last_activity_human' => $lastActivity->diffForHumans(),
                    'last_activity_formatted' => $lastActivity->format('d M Y, h:i A'),
                ];
            });
    }

    public function getProfileStats(string $userId): array
    {
        $cacheKey = 'profile_stats_'.$userId;

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId): array {
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();

            $tripsCreated = Trip::query()
                ->where('created_by', $userId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $paymentsRecorded = Payment::query()
                ->where('collected_by', $userId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $invoicesGenerated = Invoice::query()
                ->where('issued_by', $userId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $totalActions = AuditLog::query()
                ->where('user_id', $userId)
                ->count();

            return [
                'trips_created' => $tripsCreated,
                'payments_recorded' => $paymentsRecorded,
                'invoices_generated' => $invoicesGenerated,
                'total_actions' => $totalActions,
            ];
        });
    }

    public function getActivityLog(string $userId, array $filters): LengthAwarePaginator
    {
        return $this->buildActivityLogQuery($userId, $filters)
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function getActivityLogCollection(string $userId, array $filters): Collection
    {
        return $this->buildActivityLogQuery($userId, $filters)
            ->orderByDesc('created_at')
            ->get();
    }

    private function buildActivityLogQuery(string $userId, array $filters)
    {
        return AuditLog::query()
            ->where('user_id', $userId)
            ->when(filled($filters['from'] ?? null), fn ($query) => $query->whereDate('created_at', '>=', (string) $filters['from']))
            ->when(filled($filters['to'] ?? null), fn ($query) => $query->whereDate('created_at', '<=', (string) $filters['to']))
            ->when(filled($filters['type'] ?? null) && (string) $filters['type'] !== 'all', function ($query) use ($filters): void {
                $type = (string) $filters['type'];

                if ($type === 'trip') {
                    $query->where('action', 'like', 'trip%');

                    return;
                }

                if ($type === 'payment') {
                    $query->where('action', 'like', 'payment%');

                    return;
                }

                if ($type === 'profile') {
                    $query->where('action', 'like', 'profile%');

                    return;
                }

                if ($type === 'auth') {
                    $query->where('action', 'like', 'auth%')
                        ->orWhere('action', 'like', 'login%');

                    return;
                }

                if ($type === 'other') {
                    $query->where(function ($nested): void {
                        $nested->where('action', 'not like', 'trip%')
                            ->where('action', 'not like', 'payment%')
                            ->where('action', 'not like', 'profile%')
                            ->where('action', 'not like', 'auth%')
                            ->where('action', 'not like', 'login%');
                    });
                }
            });
    }
}

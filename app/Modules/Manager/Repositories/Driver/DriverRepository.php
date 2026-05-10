<?php

declare(strict_types=1);

namespace App\Modules\Manager\Repositories\Driver;

use App\Modules\Auth\Models\User as AuthUser;
use App\Modules\Driver\Models\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DriverRepository implements DriverRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Driver::query();
        $table = $query->getModel()->getTable();

        $hasName = Schema::hasColumn($table, 'name');
        $hasMobileNumber = Schema::hasColumn($table, 'mobile_number');
        $hasStatus = Schema::hasColumn($table, 'status');
        $hasIsApproved = Schema::hasColumn($table, 'is_approved');
        $hasCreatedBy = Schema::hasColumn($table, 'created_by');
        $hasUserId = Schema::hasColumn($table, 'user_id');

        $shouldJoinUsers = $hasUserId && (! $hasName || ! $hasMobileNumber || ! $hasStatus || ! $hasIsApproved || ! $hasCreatedBy);

        if ($shouldJoinUsers) {
            $query->leftJoin('users', 'users.id', '=', "{$table}.user_id");
        }

        $query->select(["{$table}.id", "{$table}.driving_type", "{$table}.joining_date", "{$table}.created_at"]);

        if ($hasName) {
            $query->addSelect("{$table}.name");
        } elseif ($shouldJoinUsers) {
            $query->addSelect('users.name as name');
        } else {
            $query->selectRaw("'' as name");
        }

        if ($hasMobileNumber) {
            $query->addSelect("{$table}.mobile_number");
        } elseif ($shouldJoinUsers) {
            $query->addSelect('users.phone as mobile_number');
        } else {
            $query->selectRaw("'' as mobile_number");
        }

        if ($hasStatus) {
            $query->addSelect("{$table}.status");
        } elseif ($shouldJoinUsers) {
            $query->selectRaw("CASE WHEN users.is_active = 1 THEN 'active' ELSE 'inactive' END as status");
        } else {
            $query->selectRaw("'active' as status");
        }

        if ($hasIsApproved) {
            $query->addSelect("{$table}.is_approved");
        } elseif ($shouldJoinUsers) {
            $query->selectRaw('CASE WHEN users.approved_at IS NULL THEN 0 ELSE 1 END as is_approved');
        } else {
            $query->selectRaw('0 as is_approved');
        }

        if (filled($filters['driving_type'] ?? null)) {
            $query->where("{$table}.driving_type", (string) $filters['driving_type']);
        }

        if (filled($filters['status'] ?? null)) {
            if ($hasStatus) {
                $query->where("{$table}.status", (string) $filters['status']);
            } elseif ($shouldJoinUsers) {
                $query->where('users.is_active', (string) $filters['status'] === 'active');
            }
        }

        if (filled($filters['is_approved'] ?? null)) {
            $approved = filter_var($filters['is_approved'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if (is_bool($approved)) {
                if ($hasIsApproved) {
                    $query->where("{$table}.is_approved", $approved);
                } elseif ($shouldJoinUsers) {
                    if ($approved) {
                        $query->whereNotNull('users.approved_at');
                    } else {
                        $query->whereNull('users.approved_at');
                    }
                }
            }
        }

        if (filled($filters['search'] ?? null)) {
            $search = (string) $filters['search'];

            $query->where(function (Builder $searchQuery) use ($table, $hasName, $hasMobileNumber, $shouldJoinUsers, $search): void {
                $hasAnyCondition = false;

                if ($hasName) {
                    $searchQuery->where("{$table}.name", 'like', "%{$search}%");
                    $hasAnyCondition = true;
                } elseif ($shouldJoinUsers) {
                    $searchQuery->where('users.name', 'like', "%{$search}%");
                    $hasAnyCondition = true;
                }

                if ($hasMobileNumber) {
                    if ($hasAnyCondition) {
                        $searchQuery->orWhere("{$table}.mobile_number", 'like', "%{$search}%");
                    } else {
                        $searchQuery->where("{$table}.mobile_number", 'like', "%{$search}%");
                    }
                } elseif ($shouldJoinUsers) {
                    $searchQuery->orWhere('users.phone', 'like', "%{$search}%");
                }
            });
        }

        return $query
            ->orderBy("{$table}.created_at", 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): Driver
    {
        return Driver::query()->findOrFail($id);
    }

    public function findWithStats(int $id): Driver
    {
        return Driver::query()
            ->with(['user:id,name,phone,is_active,approved_at', 'trips' => function ($query): void {
                $query->latest()->limit(10)->select([
                    'id',
                    'driver_id',
                    'client_id',
                    'status_id',
                    'trip_code',
                    'load_date',
                    'trip_rate',
                    'profit',
                ])->with(['status:id,name', 'client:id,company_name']);
            }])
            ->withCount('trips')
            ->withSum('trips', 'trip_rate')
            ->withSum('trips', 'profit')
            ->findOrFail($id);
    }

    public function create(array $data): Driver
    {
        $table = (new Driver)->getTable();

        if (! Schema::hasColumn($table, 'name') && Schema::hasColumn($table, 'user_id')) {
            $status = (string) ($data['status'] ?? 'active');
            $isApproved = (bool) ($data['is_approved'] ?? false);
            $phone = (string) ($data['mobile_number'] ?? '');
            $baseEmail = $phone !== '' ? 'driver'.$phone.'@tms.local' : 'driver'.Str::lower(Str::random(10)).'@tms.local';
            $email = $baseEmail;

            while (AuthUser::query()->where('email', $email)->exists()) {
                $email = Str::before($baseEmail, '@').'_'.Str::lower(Str::random(6)).'@'.Str::after($baseEmail, '@');
            }

            $user = AuthUser::query()->create([
                'name' => (string) ($data['name'] ?? 'Driver'),
                'email' => $email,
                'phone' => $phone,
                'password_hash' => Hash::make(Str::password(16)),
                'role' => 'driver',
                'is_active' => $status === 'active',
                'approved_by' => $isApproved ? Auth::id() : null,
                'approved_at' => $isApproved ? now() : null,
            ]);

            $driver = new Driver;
            $driver->forceFill([
                'user_id' => $user->id,
                'license_number' => $data['license_number'] ?? null,
                'nid_number' => $data['nid_number'] ?? null,
                'driving_type' => $data['driving_type'] ?? null,
                'joining_date' => $data['joining_date'] ?? null,
                'is_available' => $status === 'active',
            ]);
            $driver->save();

            return $driver->refresh();
        }

        $driver = new Driver;
        $driver->forceFill($data);
        $driver->save();

        return $driver->refresh();
    }

    public function update(Driver $driver, array $data): Driver
    {
        $table = $driver->getTable();

        if (! Schema::hasColumn($table, 'name') && Schema::hasColumn($table, 'user_id')) {
            $status = (string) ($data['status'] ?? $driver->status);
            $isApproved = array_key_exists('is_approved', $data)
                ? (bool) $data['is_approved']
                : $driver->is_approved;

            $driver->forceFill([
                'license_number' => $data['license_number'] ?? $driver->license_number,
                'nid_number' => $data['nid_number'] ?? $driver->nid_number,
                'driving_type' => $data['driving_type'] ?? $driver->driving_type,
                'joining_date' => $data['joining_date'] ?? $driver->joining_date,
                'is_available' => $status === 'active',
            ]);
            $driver->save();

            if ((int) $driver->user_id > 0) {
                $user = AuthUser::query()->find((int) $driver->user_id);

                if ($user !== null) {
                    $user->forceFill([
                        'name' => $data['name'] ?? $user->name,
                        'phone' => $data['mobile_number'] ?? $user->phone,
                        'is_active' => $status === 'active',
                        'approved_by' => $isApproved ? ($user->approved_by ?? Auth::id()) : null,
                        'approved_at' => $isApproved ? ($user->approved_at ?? now()) : null,
                    ]);
                    $user->save();
                }
            }

            return $driver->refresh();
        }

        $driver->forceFill($data);
        $driver->save();

        return $driver->refresh();
    }

    public function softDelete(Driver $driver): bool
    {
        return (bool) $driver->delete();
    }

    public function toggleStatus(Driver $driver): Driver
    {
        $table = $driver->getTable();

        if (Schema::hasColumn($table, 'status')) {
            $nextStatus = $driver->status === 'active' ? 'inactive' : 'active';
            $driver->update(['status' => $nextStatus]);

            return $driver->refresh();
        }

        if (Schema::hasColumn($table, 'user_id')) {
            $user = AuthUser::query()->find((int) $driver->user_id);

            if ($user !== null) {
                $user->is_active = ! $user->is_active;
                $user->save();

                if (Schema::hasColumn($table, 'is_available')) {
                    $driver->is_available = $user->is_active;
                    $driver->save();
                }

                $driver->setAttribute('status', $user->is_active ? 'active' : 'inactive');
            }
        }

        return $driver->refresh();
    }

    public function toggleApproval(Driver $driver): Driver
    {
        $table = $driver->getTable();

        if (Schema::hasColumn($table, 'is_approved')) {
            $driver->update(['is_approved' => ! $driver->is_approved]);

            return $driver->refresh();
        }

        if (Schema::hasColumn($table, 'user_id')) {
            $user = AuthUser::query()->find((int) $driver->user_id);

            if ($user !== null) {
                if ($user->approved_at === null) {
                    $user->approved_at = now();
                    $user->approved_by = Auth::id();
                } else {
                    $user->approved_at = null;
                    $user->approved_by = null;
                }

                $user->save();
            }
        }

        return $driver->refresh();
    }

    public function hasActiveTrip(Driver $driver): bool
    {
        return $driver->trips()
            ->whereHas('status', function (Builder $query): void {
                $query->where('name', 'active');
            })
            ->exists();
    }

    public function getAssignableDrivers(): Collection
    {
        $query = Driver::query()->with('user:id,name,phone,is_active,approved_at');
        $table = (new Driver)->getTable();

        if (Schema::hasColumn($table, 'status')) {
            $query->where('status', 'active');
        } elseif (Schema::hasColumn($table, 'user_id')) {
            $query->whereHas('user', function (Builder $userQuery): void {
                $userQuery->where('is_active', true);
            });
        }

        if (Schema::hasColumn($table, 'is_approved')) {
            $query->where('is_approved', true);
        } elseif (Schema::hasColumn($table, 'user_id')) {
            $query->whereHas('user', function (Builder $userQuery): void {
                $userQuery->whereNotNull('approved_at');
            });
        }

        return $query->orderBy('id')->get();
    }
}

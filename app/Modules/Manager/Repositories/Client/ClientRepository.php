<?php

declare(strict_types=1);

namespace App\Modules\Manager\Repositories\Client;

use App\Modules\Auth\Models\User as AuthUser;
use App\Modules\Client\Models\Client;
use App\Modules\Client\Models\ClientCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Client::query();
        $table = $query->getModel()->getTable();

        $hasName = Schema::hasColumn($table, 'name');
        $hasCompanyName = Schema::hasColumn($table, 'company_name');
        $hasContactNumber = Schema::hasColumn($table, 'contact_number');
        $hasClientType = Schema::hasColumn($table, 'client_type');
        $hasStatus = Schema::hasColumn($table, 'status');
        $hasProject = Schema::hasColumn($table, 'project');
        $hasProjectName = Schema::hasColumn($table, 'project_name');
        $hasUserId = Schema::hasColumn($table, 'user_id');

        $shouldJoinUsers = $hasUserId && (! $hasName || ! $hasContactNumber);

        if ($shouldJoinUsers) {
            $query->leftJoin('users', 'users.id', '=', "{$table}.user_id");
        }

        $query->select(["{$table}.id", "{$table}.created_at"]);

        if ($hasName) {
            $query->addSelect("{$table}.name");
        } elseif ($hasCompanyName) {
            $query->addSelect("{$table}.company_name as name");
        } elseif ($shouldJoinUsers) {
            $query->addSelect('users.name as name');
        } else {
            $query->selectRaw("'' as name");
        }

        if ($hasContactNumber) {
            $query->addSelect("{$table}.contact_number");
        } elseif ($shouldJoinUsers) {
            $query->addSelect('users.phone as contact_number');
        } else {
            $query->selectRaw("'' as contact_number");
        }

        if ($hasClientType) {
            $query->addSelect("{$table}.client_type");
        } else {
            $query->selectRaw("'port' as client_type");
        }

        if ($hasProject) {
            $query->addSelect("{$table}.project");
        } elseif ($hasProjectName) {
            $query->addSelect("{$table}.project_name as project");
        } else {
            $query->selectRaw('null as project');
        }

        if ($hasStatus) {
            $query->addSelect("{$table}.status");
        } else {
            $query->selectRaw("'active' as status");
        }

        if ($hasClientType && filled($filters['client_type'] ?? null)) {
            $query->where("{$table}.client_type", (string) $filters['client_type']);
        }

        if ($hasStatus && filled($filters['status'] ?? null)) {
            $query->where("{$table}.status", (string) $filters['status']);
        }

        if (filled($filters['search'] ?? null)) {
            $search = (string) $filters['search'];

            $query->where(function (Builder $searchQuery) use ($hasName, $hasCompanyName, $hasContactNumber, $shouldJoinUsers, $table, $search): void {
                $hasAnyCondition = false;

                if ($hasName) {
                    $searchQuery->where("{$table}.name", 'like', "%{$search}%");
                    $hasAnyCondition = true;
                } elseif ($hasCompanyName) {
                    $searchQuery->where("{$table}.company_name", 'like', "%{$search}%");
                    $hasAnyCondition = true;
                }

                if ($hasContactNumber) {
                    if ($hasAnyCondition) {
                        $searchQuery->orWhere("{$table}.contact_number", 'like', "%{$search}%");
                    } else {
                        $searchQuery->where("{$table}.contact_number", 'like', "%{$search}%");
                        $hasAnyCondition = true;
                    }
                }

                if ($shouldJoinUsers) {
                    if ($hasAnyCondition) {
                        $searchQuery->orWhere('users.name', 'like', "%{$search}%")
                            ->orWhere('users.phone', 'like', "%{$search}%");
                    } else {
                        $searchQuery->where('users.name', 'like', "%{$search}%")
                            ->orWhere('users.phone', 'like', "%{$search}%");
                    }
                }
            });
        }

        return $query
            ->orderBy("{$table}.created_at", 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): Client
    {
        return Client::query()->findOrFail($id);
    }

    public function findWithStats(int $id): Client
    {
        return Client::query()
            ->withCount('trips')
            ->withCount('payments')
            ->withSum('trips', 'trip_rate')
            ->withSum('trips', 'due_amount')
            ->with([
                'user:id,name,phone',
                'category:id,name',
                'trips' => function ($query): void {
                    $query
                        ->latest()
                        ->limit(10)
                        ->select([
                            'id',
                            'client_id',
                            'status_id',
                            'load_date',
                            'pickup_point',
                            'delivery_point',
                            'trip_rate',
                            'due_amount',
                        ])
                        ->with(['status:id,name']);
                },
            ])
            ->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Client
    {
        $table = (new Client)->getTable();

        if (! Schema::hasColumn($table, 'name') && Schema::hasColumn($table, 'user_id')) {
            $status = (string) ($data['status'] ?? 'active');
            $phone = (string) ($data['contact_number'] ?? '');
            $baseEmail = $phone !== '' ? 'client'.$phone.'@tms.local' : 'client'.Str::lower(Str::random(10)).'@tms.local';
            $email = $baseEmail;

            while (AuthUser::query()->where('email', $email)->exists()) {
                $email = Str::before($baseEmail, '@').'_'.Str::lower(Str::random(6)).'@'.Str::after($baseEmail, '@');
            }

            $user = AuthUser::query()->create([
                'name' => (string) ($data['name'] ?? 'Client'),
                'email' => $email,
                'phone' => $phone,
                'password_hash' => Hash::make(Str::password(16)),
                'role' => 'client',
                'is_active' => $status === 'active',
                'approved_by' => $data['created_by'] ?? null,
                'approved_at' => now(),
            ]);

            $categoryName = match ((string) ($data['client_type'] ?? 'port')) {
                'contractual' => 'Contractual Client',
                'mega_project' => 'Mega Project Client',
                default => 'Port Client',
            };

            $category = ClientCategory::query()->firstOrCreate(
                ['name' => $categoryName],
                ['description' => 'Auto-created by manager client management.']
            );

            $legacyData = [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'company_name' => $data['name'] ?? null,
                'project_name' => $data['project'] ?? null,
                'agreement_number' => $data['project_agreement_number'] ?? null,
                'project_value' => $data['project_value'] ?? null,
                'project_end_date' => $data['target_finishing_date'] ?? null,
            ];

            $client = new Client;
            $client->forceFill($legacyData);
            $client->save();

            return $client->refresh();
        }

        $client = new Client;
        $client->forceFill($data);
        $client->save();

        return $client->refresh();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Client $client, array $data): Client
    {
        $table = $client->getTable();

        if (! Schema::hasColumn($table, 'name') && Schema::hasColumn($table, 'user_id')) {
            $status = (string) ($data['status'] ?? 'active');
            $legacyData = [
                'company_name' => $data['name'] ?? $client->company_name,
                'project_name' => $data['project'] ?? null,
                'agreement_number' => $data['project_agreement_number'] ?? null,
                'project_value' => $data['project_value'] ?? null,
                'project_end_date' => $data['target_finishing_date'] ?? null,
            ];

            if (isset($data['client_type'])) {
                $categoryName = match ((string) $data['client_type']) {
                    'contractual' => 'Contractual Client',
                    'mega_project' => 'Mega Project Client',
                    default => 'Port Client',
                };

                $category = ClientCategory::query()->firstOrCreate(
                    ['name' => $categoryName],
                    ['description' => 'Auto-created by manager client management.']
                );

                $legacyData['category_id'] = $category->id;
            }

            $client->forceFill($legacyData);
            $client->save();

            if ((int) $client->user_id > 0) {
                $user = AuthUser::query()->find((int) $client->user_id);

                if ($user !== null) {
                    $user->forceFill([
                        'name' => $data['name'] ?? $user->name,
                        'phone' => $data['contact_number'] ?? $user->phone,
                        'is_active' => $status === 'active',
                    ]);
                    $user->save();
                }
            }

            return $client->refresh();
        }

        $client->forceFill($data);
        $client->save();

        return $client->refresh();
    }

    public function softDelete(Client $client): bool
    {
        return (bool) $client->delete();
    }

    public function toggleStatus(Client $client): Client
    {
        $table = $client->getTable();

        if (! Schema::hasColumn($table, 'status') && Schema::hasColumn($table, 'user_id')) {
            $user = AuthUser::query()->find((int) $client->user_id);

            if ($user !== null) {
                $user->is_active = ! $user->is_active;
                $user->save();
                $client->setAttribute('status', $user->is_active ? 'active' : 'inactive');

                return $client;
            }

            $client->setAttribute('status', 'inactive');

            return $client;
        }

        $nextStatus = $client->status === 'active' ? 'inactive' : 'active';

        $client->update(['status' => $nextStatus]);

        return $client->refresh();
    }

    public function hasTrips(Client $client): bool
    {
        return $client->trips()->exists();
    }
}

<?php

namespace App\Modules\Client\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Due\Models\DueRecord;
use App\Modules\Payment\Models\Payment;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'user_id',
        'category_id',
        'company_name',
        'project_name',
        'agreement_number',
        'project_value',
        'project_start_date',
        'project_end_date',
        'total_trips',
        'total_business_amount',
        'total_due',
    ];

    protected function casts(): array
    {
        return [
            'project_value' => 'decimal:2',
            'project_start_date' => 'date',
            'project_end_date' => 'date',
            'total_trips' => 'integer',
            'total_business_amount' => 'decimal:2',
            'total_due' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClientCategory::class, 'category_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function dueRecords(): HasMany
    {
        return $this->hasMany(DueRecord::class);
    }

    /**
     * Accessors for form-friendly field names
     */
    public function getNameAttribute(?string $value): ?string
    {
        return $value ?? $this->company_name ?? $this->user?->name;
    }

    public function getContactNumberAttribute(?string $value): ?string
    {
        return $value ?? $this->user?->phone;
    }

    public function getClientTypeAttribute(?string $value): string
    {
        if (filled($value)) {
            return (string) $value;
        }

        return match ($this->category?->name) {
            'Contractual Client' => 'contractual',
            'Mega Project Client' => 'mega_project',
            default => 'port',
        };
    }

    public function getProjectAttribute(?string $value): ?string
    {
        return $value ?? $this->project_name;
    }

    public function getProjectAgreementNumberAttribute(?string $value): ?string
    {
        return $value ?? $this->agreement_number;
    }

    public function getTargetFinishingDateAttribute(?string $value): ?string
    {
        if (filled($value)) {
            return $value;
        }

        return $this->project_end_date?->format('Y-m-d');
    }

    public function getStatusAttribute(?string $value): string
    {
        return $value ?? 'active';
    }
}

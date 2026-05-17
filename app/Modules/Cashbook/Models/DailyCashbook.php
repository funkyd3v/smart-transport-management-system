<?php

namespace App\Modules\Cashbook\Models;

use App\Models\User;
use App\Modules\Cashbook\Enums\CashbookType;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCashbook extends Model
{
    use HasUlid;

    protected $table = 'daily_cashbooks';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'ulid',
        'reference_id',
        'reference_type',
        'type',
        'amount',
        'balance',
        'description',
        'entry_date',
        'recorded_by',
        'note',
        'is_void',
        'voided_at',
        'voided_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (blank($model->id)) {
                $model->id = (string) $model->ulid;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'type' => CashbookType::class,
            'amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'entry_date' => 'date',
            'is_void' => 'boolean',
            'voided_at' => 'datetime',
        ];
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForDateRange(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        return $query
            ->when($fromDate !== null, fn (Builder $builder): Builder => $builder->whereDate('entry_date', '>=', $fromDate))
            ->when($toDate !== null, fn (Builder $builder): Builder => $builder->whereDate('entry_date', '<=', $toDate));
    }
}

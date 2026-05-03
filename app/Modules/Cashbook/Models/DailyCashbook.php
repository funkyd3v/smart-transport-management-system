<?php

namespace App\Modules\Cashbook\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCashbook extends Model
{
    use HasUlid;

    protected $table = 'daily_cashbook';

    protected $fillable = [
        'ulid',
        'entry_date',
        'recorded_by',
        'total_income',
        'total_expense',
        'total_due_collected',
        'spare_income',
        'net_profit',
        'opening_balance',
        'closing_balance',
        'is_finalized',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'total_income' => 'decimal:2',
            'total_expense' => 'decimal:2',
            'total_due_collected' => 'decimal:2',
            'spare_income' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'is_finalized' => 'boolean',
            'finalized_at' => 'datetime',
        ];
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Models\User;
use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripExpense extends Model
{
    use HasUlid;

    protected $table = 'trip_expenses';

    protected $fillable = [
        'ulid',
        'trip_id',
        'category_id',
        'recorded_by',
        'amount',
        'description',
        'expense_date',
        'receipt_path',
        'is_approved',
        'is_rejected',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'is_approved' => 'boolean',
            'is_rejected' => 'boolean',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function isPending(): bool
    {
        return ! $this->is_approved && ! $this->is_rejected;
    }

    public function isRejected(): bool
    {
        return (bool) $this->is_rejected;
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

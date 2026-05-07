<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Modules\Auth\Models\User;
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
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
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

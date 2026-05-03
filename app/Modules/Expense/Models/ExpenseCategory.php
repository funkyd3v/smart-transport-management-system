<?php

namespace App\Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function tripExpenses(): HasMany
    {
        return $this->hasMany(TripExpense::class, 'category_id');
    }
}

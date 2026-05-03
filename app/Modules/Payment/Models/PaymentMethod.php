<?php

namespace App\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_method_id');
    }
}

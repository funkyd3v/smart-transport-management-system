<?php

namespace App\Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCategory extends Model
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

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'category_id');
    }
}

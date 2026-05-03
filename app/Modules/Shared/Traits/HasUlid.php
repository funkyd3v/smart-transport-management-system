<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Support\Str;

trait HasUlid
{
    protected static function bootHasUlid(): void
    {
        static::creating(function (object $model): void {
            if (blank($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }
}

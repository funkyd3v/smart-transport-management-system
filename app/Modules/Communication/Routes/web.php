<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    // Communication module routes are intentionally minimal for now.
});

<?php

use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('trips.{tripUlid}.tracking', function ($user, string $tripUlid) {
    $trip = Trip::query()->with('status:id,name')->where('ulid', $tripUlid)->first();

    if ($trip === null) {
        return false;
    }

    return Gate::forUser($user)->allows('view', $trip);
});

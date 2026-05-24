<?php

declare(strict_types=1);

namespace App\Modules\Spare\Observers;

use App\Models\User;
use App\Modules\Notification\Models\Notification;
use App\Modules\Spare\Models\SparePart;

class SparePartObserver
{
    public function updated(SparePart $sparePart): void
    {
        if (! $sparePart->wasChanged('quantity')) {
            return;
        }

        if (
            $sparePart->quantity > SparePart::LOW_STOCK_THRESHOLD
            || (int) $sparePart->getOriginal('quantity') <= SparePart::LOW_STOCK_THRESHOLD
        ) {
            return;
        }

        $inventoryUrl = route('admin.spare.inventory.edit', $sparePart);
        $message = sprintf(
            'Low Stock Alert: "%s" has only %d unit(s) remaining. Review: %s',
            $sparePart->name,
            (int) $sparePart->quantity,
            $inventoryUrl,
        );

        User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->select(['id'])
            ->each(function (User $admin) use ($message): void {
                Notification::query()->create([
                    'user_id' => $admin->id,
                    'trip_id' => null,
                    'type' => 'system',
                    'channel' => 'due_reminder',
                    'recipient_phone' => null,
                    'message' => $message,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            });
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions;

use App\Modules\Due\Models\DueRecord;

final class RecordDuePaymentAction
{
    public function execute(DueRecord $dueRecord, float $amount): DueRecord
    {
        $newCollected = (float) $dueRecord->collected_amount + $amount;
        $remaining = max(0, (float) $dueRecord->original_due - $newCollected);

        $dueRecord->forceFill([
            'collected_amount' => $newCollected,
            'remaining_due' => $remaining,
            'is_settled' => $remaining <= 0,
            'settled_at' => $remaining <= 0 ? now() : null,
        ])->save();

        return $dueRecord->refresh();
    }
}

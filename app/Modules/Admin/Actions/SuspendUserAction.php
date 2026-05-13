<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions;

use App\Models\User;

final class SuspendUserAction
{
    public function execute(User $user): User
    {
        $user->forceFill(['is_active' => false])->save();

        return $user->refresh();
    }
}

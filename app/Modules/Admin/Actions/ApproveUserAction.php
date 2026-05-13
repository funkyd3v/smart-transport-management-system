<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions;

use App\Models\User;

final class ApproveUserAction
{
    public function execute(User $user): User
    {
        $user->forceFill(['is_active' => true])->save();

        return $user->refresh();
    }
}

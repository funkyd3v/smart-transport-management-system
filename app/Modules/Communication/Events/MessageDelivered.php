<?php

declare(strict_types=1);

namespace App\Modules\Communication\Events;

use App\Modules\Communication\Models\Communication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDelivered
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Communication $communication) {}
}

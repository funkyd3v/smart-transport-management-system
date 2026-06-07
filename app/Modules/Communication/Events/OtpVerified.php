<?php

declare(strict_types=1);

namespace App\Modules\Communication\Events;

use App\Modules\Communication\Models\OtpCode;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpVerified
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public OtpCode $otpCode) {}
}

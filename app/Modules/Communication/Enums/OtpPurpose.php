<?php

declare(strict_types=1);

namespace App\Modules\Communication\Enums;

enum OtpPurpose: string
{
    case Login = 'login';
    case Registration = 'registration';
    case PasswordReset = 'password_reset';
    case PhoneVerification = 'phone_verification';
    case TwoFactor = 'two_factor';
}

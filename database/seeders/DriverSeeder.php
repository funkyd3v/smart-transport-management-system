<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('DRIVER_EMAIL', 'driver@demo.com')],
            [
                'ulid' => (string) Str::ulid(),
                'name' => env('DRIVER_NAME', 'Driver'),
                'phone' => env('DRIVER_PHONE', '01000000002'),
                'password_hash' => Hash::make(env('DRIVER_PASSWORD', 'driver@26')),
                'role' => 'driver',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}

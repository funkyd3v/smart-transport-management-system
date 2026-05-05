<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@demo.com')],
            [
                'ulid' => (string) Str::ulid(),
                'name' => env('ADMIN_NAME', 'System Admin'),
                'phone' => env('ADMIN_PHONE', '01000000000'),
                'password_hash' => Hash::make(env('ADMIN_PASSWORD', 'admin@26')),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}

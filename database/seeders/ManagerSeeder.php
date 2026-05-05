<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('MANAGER_EMAIL', 'manager@demo.com')],
            [
                'ulid' => (string) Str::ulid(),
                'name' => env('MANAGER_NAME', 'Manager'),
                'phone' => env('MANAGER_PHONE', '01000000001'),
                'password_hash' => Hash::make(env('MANAGER_PASSWORD', 'manager@26')),
                'role' => 'manager',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}

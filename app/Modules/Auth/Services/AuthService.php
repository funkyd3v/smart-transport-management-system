<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Admin\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuthService
{
    public function recordSuccessfulLogin(User $user, Request $request): void
    {
        try {
            if (Schema::hasTable('login_histories')) {
                LoginHistory::query()->create([
                    'user_id' => $user->id,
                    'ip_address' => (string) $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'status' => 'success',
                    'created_at' => now(),
                ]);
            }

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => (string) $request->ip(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Login history recording failed on successful login.', [
                'user_id' => $user->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    public function recordFailedLoginAttempt(Request $request): void
    {
        try {
            if (! Schema::hasTable('login_histories')) {
                return;
            }

            $email = (string) $request->input('email');
            if ($email === '') {
                return;
            }

            $user = User::query()->where('email', $email)->first();
            if ($user === null) {
                return;
            }

            LoginHistory::query()->create([
                'user_id' => $user->id,
                'ip_address' => (string) $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'status' => 'failed',
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Login history recording failed on failed login attempt.', [
                'email' => (string) $request->input('email'),
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectTo($request->user()).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($this->redirectTo($request->user()).'?verified=1');
    }

    private function redirectTo(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'manager' => route('manager.dashboard', absolute: false),
            'driver' => route('driver.dashboard', absolute: false),
            default => route('home', absolute: false),
        };
    }
}

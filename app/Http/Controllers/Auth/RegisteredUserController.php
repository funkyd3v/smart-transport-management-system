<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->phone = $this->generateUniqueRegistrationPhone();
        $user->save();

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    private function generateUniqueRegistrationPhone(): string
    {
        do {
            $phone = '9'.str_pad((string) random_int(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (User::query()->where('phone', $phone)->exists());

        return $phone;
    }
}

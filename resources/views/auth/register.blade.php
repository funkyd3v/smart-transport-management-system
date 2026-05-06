<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up | {{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="favicon.ico">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    
    <style>
        /* Animated Gradient for the TransCorp Logo */
        @keyframes logo-gradient {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        .animate-logo {
            background-size: 200% auto;
            animation: logo-gradient 3s linear infinite;
        }

        @keyframes button-shimmer {
            0% { transform: translateX(-140%); }
            100% { transform: translateX(180%); }
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-900">
    <!-- Preloader -->
    <div x-data="{ loaded: true }" x-show="loaded"
         x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
         class="fixed left-0 top-0 z-50 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-[#DF7F07] border-t-transparent"></div>
    </div>

    <!-- Page Wrapper -->
    <div class="relative p-6 bg-white dark:bg-gray-900 sm:p-0">
        <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
            
            <!-- Form Section (Left side on desktop) -->
            <div class="flex flex-col flex-1 w-full lg:w-1/2">
                <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                    <div>
                        <div class="mb-5 sm:mb-8">
                            <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                {{ __('Create Account') }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Join TransCorp and streamline your logistics today.') }}
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="mb-4 text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        {{ __('Full Name') }}<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                           placeholder="{{ __('John Doe') }}"
                                           class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                           required autofocus>
                                    @error('name')
                                        <span class="text-sm text-error-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        {{ __('Email Address') }}<span class="text-error-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                           placeholder="{{ __('info@gmail.com') }}"
                                           class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                           required>
                                    @error('email')
                                        <span class="text-sm text-error-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        {{ __('Password') }}<span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <input :type="showPassword ? 'text' : 'password'" name="password"
                                               placeholder="{{ __('Create a strong password') }}"
                                               class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                               required>
                                        <span @click="showPassword = !showPassword"
                                              class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                                            <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3"/></svg>
                                            <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z" fill="#98A2B3"/></svg>
                                        </span>
                                    </div>
                                    @error('password')
                                        <span class="text-sm text-error-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        {{ __('Confirm Password') }}<span class="text-error-500">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation"
                                           placeholder="{{ __('Repeat your password') }}"
                                           class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                           required>
                                </div>

                                <!-- Register Button -->
                                <div class="pt-2">
                                    <button type="submit"
                                        style="background-color: #D97C07 !important; border-color: #D97C07 !important;"
                                        class="relative group isolate overflow-hidden flex items-center justify-center w-full px-4 py-3 text-sm font-bold !text-white transition rounded-lg border shadow-md hover:!bg-[#b06506] focus:outline-hidden focus:ring-3 focus:ring-[#D97C07]/25">
                                        <span class="pointer-events-none absolute inset-y-0 w-1/2 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-[button-shimmer_2.2s_linear_infinite]"></span>
                                        <span class="relative z-10">{{ __('Create Account') }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="mt-5">
                            <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
                                {{ __('Already have an account?') }}
                                <a href="{{ route('login') }}"
                                   class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                    {{ __('Sign In') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section (Identical to Login) -->
            <div class="relative hidden lg:flex items-center justify-center w-full min-h-screen lg:w-1/2 overflow-hidden"
                style="background: radial-gradient(circle at top left, #DF7F07 0%, #4a2a02 35%, #0f172a 100%); font-family: 'Roboto', sans-serif;">
                
                <div class="absolute inset-0 opacity-10 pointer-events-none" 
                    style="background-image: url('{{ asset('assets/images/shape/grid-01.svg') }}'); background-repeat: repeat; background-size: 400px;">
                </div>

                <div class="relative z-10 flex flex-col items-center max-w-2xl px-10 text-center">
                    <div class="mb-10">
                        <h1 class="uppercase select-none whitespace-nowrap animate-logo bg-gradient-to-r from-white via-[#FFD199] to-white bg-clip-text text-transparent"
                            style="font-size: 3rem; line-height: 0.9; font-weight: 900; letter-spacing: -0.06em; 
                                   background-size: 200% auto;
                                   filter: drop-shadow(0 10px 20px rgba(255, 255, 255, 0.4)) drop-shadow(0 0 20px rgba(163, 92, 6, 0.2));">
                            TransCorp
                        </h1>
                    </div>
                    
                    <div class="space-y-4">            
                        <div class="w-24 h-1.5 bg-gray-600 mx-auto rounded-full opacity-40"></div>
                        <p class="text-white max-w-xs mx-auto leading-relaxed"
                        style="font-size: 1.125rem; font-weight: 400;">
                            {{ __('The ultimate Transport Management System for modern logistics operations.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dark Mode Toggler -->
            <div class="fixed z-50 hidden bottom-6 right-6 sm:block">
                <button class="inline-flex items-center justify-center text-white transition-colors rounded-full size-14 bg-[#DF7F07] hover:bg-[#b06506]"
                        @click.prevent="darkMode = !darkMode" x-data="{ darkMode: false }"
                        x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')); $watch('darkMode', value => { localStorage.setItem('darkMode', JSON.stringify(value)); document.documentElement.classList.toggle('dark', value); })">
                    <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327Z" fill=""/></svg>
                </button>
            </div>
        </div>
    </div>
    <script defer src="{{ asset('assets/js/bundle.js') }}"></script>
</body>
</html>

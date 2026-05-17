<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Email | {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logistics/truck.png') }}" type="image/png">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @keyframes gentleFade {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: gentleFade 0.6s ease-out both; }
        .fade-in-d1 { animation: gentleFade 0.6s ease-out 0.1s both; }
        .fade-in-d2 { animation: gentleFade 0.6s ease-out 0.2s both; }
        .fade-in-d3 { animation: gentleFade 0.6s ease-out 0.3s both; }
        .fade-in-d4 { animation: gentleFade 0.6s ease-out 0.4s both; }

        @keyframes envelopeBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .envelope-bounce { animation: envelopeBounce 3s ease-in-out infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .dot-pulse { animation: pulse 2s ease-in-out infinite; }

        @keyframes toastSlide {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes toastHide {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(12px); }
        }

        .btn-primary {
            background-color: #D97C07;
            border-color: #D97C07;
            transition: all 0.2s ease;
        }
        .btn-primary:hover:not(:disabled) {
            background-color: #c47006;
            border-color: #c47006;
            box-shadow: 0 4px 16px rgba(217, 124, 7, 0.3);
        }
        .btn-primary:active:not(:disabled) {
            transform: scale(0.98);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="h-full antialiased bg-slate-50 text-slate-900 dark:bg-[#090d16] dark:text-slate-100 selection:bg-amber-500 selection:text-white">

    <!-- Soft Background -->
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-amber-50/50 via-white to-white dark:from-amber-950/10 dark:via-[#090d16] dark:to-[#090d16]"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] rounded-full bg-amber-100/40 dark:bg-amber-500/5 blur-[100px]"></div>
    </div>

    <!-- Main Content -->
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-16">

        <!-- Logo -->
        <a href="{{ route('home') }}" class="fade-in inline-flex items-center gap-2.5 mb-10 text-decoration-none group hover:opacity-80 transition-opacity">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#D97C07]">
                <i class="fas fa-truck-fast text-sm text-white"></i>
            </span>
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                Trans<span class="text-[#D97C07]">Corp</span>
            </span>
        </a>

        <!-- Card -->
        <div class="fade-in-d1 w-full max-w-[400px] bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-sm">

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="relative envelope-bounce">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                        <i class="fas fa-envelope text-2xl text-[#D97C07] dark:text-amber-400"></i>
                    </div>
                    <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-[#D97C07] rounded-full border-2 border-white dark:border-slate-900 dot-pulse"></span>
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center">
                <h1 class="text-[22px] font-bold text-slate-900 dark:text-white leading-snug">
                    Verify your email address
                </h1>
                <p class="mt-2.5 text-[15px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    We sent a verification link to
                </p>
                <p class="mt-1 text-[15px] font-semibold text-slate-800 dark:text-slate-200">
                    {{ auth()->user()?->email }}
                </p>
            </div>

            <!-- Success Banner -->
            <div id="successBanner" class="{{ session('status') === 'verification-link-sent' ? '' : 'hidden' }} mt-5 flex items-center justify-center gap-2 text-center">
                <i class="fas fa-circle-check text-green-500 text-sm"></i>
                <p class="text-sm font-medium" style="color: #16a34a;">
                    A new verification link has been sent to your email.
                </p>
            </div>

            <!-- Hint -->
            <div class="fade-in-d2 mt-5 flex items-center justify-center gap-2 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 px-4 py-3 text-center">
                <i class="fas fa-clock text-slate-400 dark:text-slate-500 text-xs shrink-0"></i>
                <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Didn't receive it? Check your spam folder, or resend the link below.
                </p>
            </div>

            <!-- Resend Button -->
            <div class="fade-in-d3 mt-7">
                <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                    @csrf
                    <button type="button" id="resendBtn" onclick="handleResend()"
                        class="btn-primary w-full flex items-center justify-center gap-2 rounded-xl py-3 px-4 text-sm font-semibold text-white cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:ring-offset-2 dark:focus:ring-offset-slate-900">

                        <span id="btnDefault" class="flex items-center gap-2">
                            <i class="fas fa-rotate-right text-xs"></i>
                            Resend verification email
                        </span>

                        <span id="btnLoading" class="hidden items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5" class="opacity-20"></circle>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="opacity-80"></path>
                            </svg>
                            Resend in <span id="countdown" class="font-bold tabular-nums inline-block w-5 text-center">60</span>s
                        </span>
                    </button>
                </form>
            </div>

            <!-- Divider + Links -->
            <div class="fade-in-d4 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-[13px] font-medium text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    Back to home
                </a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-[13px] font-medium text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    Sign out
                </a>
            </div>
        </div>

        <!-- Subtle footer -->
        <p class="fade-in-d4 mt-6 text-[12px] text-slate-400 dark:text-slate-600">
            Secured with 256-bit encryption
        </p>
    </div>

    <!-- Logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Toast container -->
    <div id="toastContainer" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none"></div>

    <script defer src="{{ asset('assets/js/bundle.js') }}"></script>
    <script>
        const resendBtn = document.getElementById('resendBtn');
        const btnDefault = document.getElementById('btnDefault');
        const btnLoading = document.getElementById('btnLoading');
        const countdownEl = document.getElementById('countdown');
        let timer = null;

        function startCountdown(sec) {
            let remaining = sec;
            resendBtn.disabled = true;
            btnDefault.classList.add('hidden');
            btnDefault.classList.remove('flex');
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');
            countdownEl.textContent = remaining;

            clearInterval(timer);
            timer = setInterval(() => {
                remaining--;
                countdownEl.textContent = remaining;
                if (remaining <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                    btnDefault.classList.remove('hidden');
                    btnDefault.classList.add('flex');
                }
            }, 1000);
        }

        async function handleResend() {
            const form = document.getElementById('resendForm');
            const token = document.querySelector('meta[name="csrf-token"]').content;

            document.getElementById('successBanner').classList.remove('hidden');
            startCountdown(60);

            try {
                await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    body: new FormData(form),
                });
            } catch (err) {
                document.getElementById('successBanner').classList.add('hidden');
                showToast('Something went wrong. Please try again.');
            }
        }

        @if (session('status') === 'verification-link-sent')
            startCountdown(60);
        @endif

        function showToast(msg) {
            const c = document.getElementById('toastContainer');
            const t = document.createElement('div');
            t.className = 'pointer-events-auto bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-sm font-medium px-4 py-2.5 rounded-xl shadow-lg';
            t.style.animation = 'toastSlide 0.3s ease-out both';
            t.textContent = msg;
            c.appendChild(t);
            setTimeout(() => {
                t.style.animation = 'toastHide 0.25s ease-in forwards';
                setTimeout(() => t.remove(), 250);
            }, 2000);
        }
    </script>
</body>
</html>
<header class="border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="flex flex-col gap-4 px-4 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Operations Workspace</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $title }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        </div>

        <div class="flex items-center gap-3 self-start rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()?->name ?? 'Guest User' }}</p>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ auth()->user()?->role ?? 'guest' }}</p>
            </div>

            <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700"
                >
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

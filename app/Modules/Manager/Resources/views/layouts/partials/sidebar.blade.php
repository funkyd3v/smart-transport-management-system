<aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white xl:block">
    <div class="flex h-20 items-center border-b border-slate-200 px-6">
        <a href="{{ route('manager.dashboard') }}" class="text-lg font-semibold tracking-tight text-slate-900">
            Ops Manager
        </a>
    </div>

    <nav class="space-y-2 px-4 py-6">
        <a href="{{ route('manager.dashboard') }}" @class([
            'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition',
            'bg-brand-600 text-white shadow-soft' => request()->routeIs('manager.dashboard'),
            'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('manager.dashboard'),
        ])>
            <span class="inline-flex size-9 items-center justify-center rounded-lg bg-white/15 text-xs font-semibold">MG</span>
            <span>Dashboard Overview</span>
        </a>
        <a href="{{ url('/manager/trips') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
            <span class="inline-flex size-9 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-700">TR</span>
            <span>Trip Coordination</span>
        </a>
        <a href="{{ url('/manager/drivers') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
            <span class="inline-flex size-9 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-700">DR</span>
            <span>Driver Allocation</span>
        </a>
    </nav>
</aside>

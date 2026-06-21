@extends('client::layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $currency = static fn (float|int|null $v): string => '৳ '.number_format((float) ($v ?? 0), 2);

        $statusMeta = static function (?string $name): array {
            return match (strtolower((string) $name)) {
                'in_progress', 'in progress' => ['In Progress', 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400'],
                'completed'                  => ['Completed',   'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400'],
                'cancelled'                  => ['Cancelled',   'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400'],
                'created'                    => ['Awaiting',    'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400'],
                default                      => ['Unknown',     'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'],
            };
        };

        $paymentsRoute = \Illuminate\Support\Facades\Route::has('client.payments.index')
            ? route('client.payments.index') : null;
    @endphp

    {{-- ─── Page heading ──────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                Welcome, {{ $client->company_name ?? auth()->user()?->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('l, d M Y') }} &nbsp;·&nbsp; Last 6-month summary
            </p>
        </div>
        @if ($paymentsRoute && $totalOutstanding > 0)
            <a href="{{ $paymentsRoute }}"
               class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <path d="M3 9h18M7 15h2m4 0h2M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Pay {{ $currency($totalOutstanding) }}
            </a>
        @endif
    </div>

    {{-- ─── KPI Cards ──────────────────────────────────────────────── --}}
    <section class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
        {{-- Active Trips --}}
        <article class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/15">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                    <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <circle cx="5.5" cy="18.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                    <circle cx="18.5" cy="18.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Active Trips</p>
                <h3 class="mt-1 text-3xl font-bold text-gray-800 dark:text-white/90">{{ $activeCount }}</h3>
            </div>
            <span class="mt-3 inline-flex w-fit rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">In Transit</span>
        </article>

        {{-- Outstanding Due --}}
        <article class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/15">
                <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24" fill="none">
                    <path d="M12 8v5l3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Outstanding Due</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $currency($totalOutstanding) }}</h3>
            </div>
            @if ($overdueCount > 0)
                <span class="mt-3 inline-flex w-fit rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:bg-red-500/15 dark:text-red-400">{{ $overdueCount }} Overdue</span>
            @else
                <span class="mt-3 inline-flex w-fit rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700 dark:bg-orange-500/15 dark:text-orange-400">{{ $unsettledCount }} Unsettled</span>
            @endif
        </article>

        {{-- Completed This Month --}}
        <article class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/15">
                <svg class="h-6 w-6 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Completed This Month</p>
                <h3 class="mt-1 text-3xl font-bold text-gray-800 dark:text-white/90">{{ $completedThisMonth }}</h3>
            </div>
            <span class="mt-3 inline-flex w-fit rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">Delivered</span>
        </article>

        {{-- Total Paid (6 months) --}}
        <article class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/15">
                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M2 10h20" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Paid (6 Months)</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $currency($totalPaidSixMonths) }}</h3>
            </div>
            <span class="mt-3 inline-flex w-fit rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-700 dark:bg-purple-500/15 dark:text-purple-400">Settled</span>
        </article>
    </section>

    {{-- ─── Main grid: Active Shipments + Outstanding Dues ─────────── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Active Shipments ──────────────────────────────────── --}}
        <div class="xl:col-span-2 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white/90">Active Shipments</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Trips currently in transit</p>
                </div>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">{{ $activeCount }} running</span>
            </div>

            @if ($activeTrips->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" fill="none">
                            <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="5.5" cy="18.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="18.5" cy="18.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No active shipments</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Your shipments will appear here once dispatched.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($activeTrips as $trip)
                        @php
                            $loc = $trip->currentVehicleLocation;
                            $hasLocation = $loc && $loc->latitude && $loc->longitude;
                        @endphp
                        <div class="group relative px-6 py-4 transition-colors hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</span>
                                        <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">In Progress</span>
                                        @if ($hasLocation && $loc->is_online)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="7" r="3" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M12 10v4m0 0c-4 0-7 1.8-7 4v1h14v-1c0-2.2-3-4-7-4z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        <span class="truncate">{{ $trip->pickup_point ?? '—' }}</span>
                                        <svg class="h-3.5 w-3.5 shrink-0 text-gray-300" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="truncate">{{ $trip->delivery_point ?? '—' }}</span>
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Truck:</span>
                                            {{ $trip->truck?->truck_number ?? '—' }}
                                        </span>
                                        <span>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Driver:</span>
                                            {{ $trip->driver?->user?->name ?? $trip->driver?->name ?? '—' }}
                                        </span>
                                        @if ($trip->load_date)
                                            <span>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Loaded:</span>
                                                {{ $trip->load_date->format('d M Y') }}
                                            </span>
                                        @endif
                                        @if ($trip->expected_delivery_date)
                                            <span>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">ETA:</span>
                                                {{ $trip->expected_delivery_date->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if ($hasLocation)
                                    <button
                                        type="button"
                                        onclick="openMapModal({{ $loc->latitude }}, {{ $loc->longitude }}, '{{ addslashes($trip->trip_code) }}', '{{ addslashes($trip->truck?->truck_number ?? 'Truck') }}')"
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        Track
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Outstanding Dues ──────────────────────────────────── --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white/90">Outstanding Dues</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Unsettled balances</p>
                </div>
                @if ($paymentsRoute)
                    <a href="{{ $paymentsRoute }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Pay Now →</a>
                @endif
            </div>

            @if ($outstandingDues->isEmpty())
                <div class="flex flex-col items-center justify-center px-5 py-14 text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/15">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none">
                            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">All settled!</p>
                    <p class="text-xs text-gray-400">No outstanding balances.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($outstandingDues as $due)
                        @php
                            $isOverdue = $due->due_date && $due->due_date->isPast();
                        @endphp
                        <div class="px-5 py-3.5 {{ $isOverdue ? 'bg-red-50/50 dark:bg-red-500/5' : '' }}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-800 dark:text-white/90">
                                        {{ $due->trip?->trip_code ?? '—' }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                        {{ \Illuminate\Support\Str::limit(($due->trip?->pickup_point ?? '').' → '.($due->trip?->delivery_point ?? ''), 36) }}
                                    </p>
                                    @if ($due->due_date)
                                        <p class="mt-0.5 text-xs {{ $isOverdue ? 'font-semibold text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}">
                                            {{ $isOverdue ? 'Overdue: ' : 'Due: ' }}{{ $due->due_date->format('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $currency($due->remaining_due) }}</p>
                                    <p class="text-xs text-gray-400">of {{ $currency($due->original_due) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($paymentsRoute)
                    <div class="border-t border-gray-100 px-5 py-3 dark:border-gray-800">
                        <a href="{{ $paymentsRoute }}"
                           class="block w-full rounded-xl bg-brand-600 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-brand-700">
                            Pay with bKash
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ─── Bottom grid: Recent Payments + Invoices + Trip History ── --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Recent Payments ───────────────────────────────────── --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h4 class="font-semibold text-gray-800 dark:text-white/90">Recent Payments</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Last 5 successful transactions</p>
            </div>

            @if ($recentPayments->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No payment records found.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($recentPayments as $payment)
                        <div class="flex items-center justify-between gap-2 px-5 py-3.5">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $currency($payment->amount) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $payment->trip?->trip_code ?? 'General' }}
                                    @if ($payment->gateway)
                                        · <span class="capitalize">{{ $payment->gateway }}</span>
                                    @endif
                                </p>
                                @if ($payment->provider_reference)
                                    <p class="mt-0.5 truncate font-mono text-[10px] text-gray-400 dark:text-gray-500">{{ $payment->provider_reference }}</p>
                                @endif
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_date?->format('d M') ?? '—' }}</p>
                                <span class="mt-1 inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">Paid</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Invoices ───────────────────────────────────── --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h4 class="font-semibold text-gray-800 dark:text-white/90">Recent Invoices</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Last 5 issued invoices</p>
            </div>

            @if ($recentInvoices->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No invoices issued yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($recentInvoices as $invoice)
                        <div class="flex items-center justify-between gap-2 px-5 py-3.5">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $invoice->invoice_number ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->trip?->trip_code ?? '—' }}</p>
                                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                    Issued {{ $invoice->issued_at?->format('d M Y') ?? '—' }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $currency($invoice->total_amount) }}</p>
                                @if ((float) $invoice->due_amount > 0)
                                    <p class="mt-0.5 text-xs text-red-500">Due: {{ $currency($invoice->due_amount) }}</p>
                                @else
                                    <p class="mt-0.5 text-xs text-green-600 dark:text-green-400">Settled</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Trip History ───────────────────────────────── --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h4 class="font-semibold text-gray-800 dark:text-white/90">Trip History</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Last 10 trips (6 months)</p>
            </div>

            @if ($recentTrips->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No trip history in the last 6 months.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($recentTrips as $trip)
                        @php [$label, $cls] = $statusMeta($trip->status?->name); @endphp
                        <div class="flex items-center justify-between gap-2 px-5 py-3.5">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit(($trip->pickup_point ?? '?').' → '.($trip->delivery_point ?? '?'), 34) }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                    {{ $trip->completed_at?->format('d M Y') ?? ($trip->load_date?->format('d M Y') ?? '—') }}
                                    · {{ $trip->truck?->truck_number ?? '—' }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $cls }}">{{ $label }}</span>
                                @if ($trip->invoice)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Invoiced</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Map Modal ──────────────────────────────────────────────── --}}
    <div id="map-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
        <div class="relative flex w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <div>
                    <h5 id="map-modal-title" class="text-base font-semibold text-gray-800 dark:text-white/90">Live Location</h5>
                    <p id="map-modal-sub" class="text-xs text-gray-500 dark:text-gray-400"></p>
                </div>
                <button onclick="closeMapModal()" type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div id="map-container" class="h-80 w-full bg-gray-100 dark:bg-gray-800"></div>
            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-3 dark:border-gray-800">
                <p id="map-coords" class="font-mono text-xs text-gray-400"></p>
                <a id="map-external-link" href="#" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-brand-700">
                    Open in Maps
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <path d="M18 13v6H6V7h6M15 3h6v6M10 14L21 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openMapModal(lat, lng, tripCode, truckNumber) {
        var modal = document.getElementById('map-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('map-modal-title').textContent = 'Live Location — ' + tripCode;
        document.getElementById('map-modal-sub').textContent = 'Truck: ' + truckNumber;
        document.getElementById('map-coords').textContent = parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5);
        document.getElementById('map-external-link').href = 'https://www.google.com/maps?q=' + lat + ',' + lng;
        document.getElementById('map-container').innerHTML =
            '<iframe class="h-full w-full border-0" loading="lazy" allowfullscreen src="https://maps.google.com/maps?q=' + lat + ',' + lng + '&z=14&output=embed"></iframe>';
    }

    function closeMapModal() {
        var modal = document.getElementById('map-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('map-container').innerHTML = '';
    }

    document.getElementById('map-modal').addEventListener('click', function(e) {
        if (e.target === this) { closeMapModal(); }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeMapModal(); }
    });
</script>
@endpush
